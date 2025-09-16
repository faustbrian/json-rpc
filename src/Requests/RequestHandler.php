<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Requests;

use Cline\JsonRpc\Data\RequestData;
use Cline\JsonRpc\Data\RequestObjectData;
use Cline\JsonRpc\Data\RequestResultData;
use Cline\JsonRpc\Data\ResponseData;
use Cline\JsonRpc\Exceptions\AbstractRequestException;
use Cline\JsonRpc\Exceptions\ExceptionMapper;
use Cline\JsonRpc\Exceptions\ForbiddenException;
use Cline\JsonRpc\Exceptions\InternalErrorException;
use Cline\JsonRpc\Exceptions\InvalidRequestException;
use Cline\JsonRpc\Exceptions\ParseErrorException;
use Cline\JsonRpc\Exceptions\UnauthorizedException;
use Cline\JsonRpc\Facades\Server;
use Cline\JsonRpc\Jobs\CallMethod;
use Cline\JsonRpc\Rules\Identifier;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Throwable;

use const JSON_THROW_ON_ERROR;

use function count;
use function data_get;
use function is_array;
use function is_string;
use function json_decode;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class RequestHandler
{
    public static function createFromArray(array $request): RequestResultData
    {
        return new self()->handle($request);
    }

    public static function createFromString(string $request): RequestResultData
    {
        return new self()->handle($request);
    }

    public function handle(array|string $request): RequestResultData
    {
        try {
            $requestBody = self::parse($request);

            if (count($requestBody->requestObjects) > 10) {
                throw InvalidRequestException::create([
                    [
                        'status' => '400',
                        'source' => ['pointer' => '/'],
                        'title' => 'Invalid request',
                        'detail' => 'The request contains too many items. The maximum is 10.',
                    ],
                ]);
            }

            /** @var array<int, Collection|ResponseData> $responses */
            $responses = [];

            foreach ($requestBody->requestObjects as $requestObject) {
                try {
                    self::validate($requestObject);

                    $requestObject = RequestObjectData::from($requestObject);

                    $method = Server::getMethodRepository()->get($requestObject->method);

                    if ($requestObject->isNotification()) {
                        CallMethod::dispatchAfterResponse($method, $requestObject);

                        // The Server MUST NOT reply to a Notification, including those that are within a batch request.
                        continue;
                    }

                    $responses[] = CallMethod::dispatchSync($method, $requestObject);
                } catch (Throwable $exception) {
                    $responses[] = ResponseData::from([
                        'jsonrpc' => '2.0',
                        'id' => data_get($requestObject, 'id'),
                        'error' => ExceptionMapper::execute($exception)->toError(),
                    ]);
                }
            }

            if (count($responses) < 1) {
                return RequestResultData::from([
                    'data' => $responses,
                    'statusCode' => 200,
                ]);
            }

            if ($requestBody->isBatch) {
                return RequestResultData::from([
                    'data' => $responses,
                    'statusCode' => 200,
                ]);
            }

            return RequestResultData::from([
                'data' => $responses[0],
                'statusCode' => 200,
            ]);
        } catch (Throwable $throwable) {
            if ($throwable instanceof AbstractRequestException) {
                return RequestResultData::from([
                    'data' => ResponseData::createFromRequestException($throwable),
                    'statusCode' => 400,
                ]);
            }

            if ($throwable instanceof AuthenticationException) {
                return RequestResultData::from([
                    'data' => ResponseData::createFromRequestException(UnauthorizedException::create()),
                    'statusCode' => 401,
                ]);
            }

            if ($throwable instanceof AuthorizationException) {
                return RequestResultData::from([
                    'data' => ResponseData::createFromRequestException(ForbiddenException::create()),
                    'statusCode' => 403,
                ]);
            }

            return RequestResultData::from([
                'data' => ResponseData::createFromRequestException(
                    InternalErrorException::create($throwable),
                ),
                'statusCode' => 500,
            ]);
        }
    }

    private static function parse(array|string $requestObjects): RequestData
    {
        if (is_string($requestObjects)) {
            try {
                $requestObjects = json_decode($requestObjects, true, 512, JSON_THROW_ON_ERROR);
            } catch (Throwable) {
                throw ParseErrorException::create();
            }
        }

        if (empty($requestObjects)) {
            throw InvalidRequestException::create();
        }

        if (!is_array($requestObjects)) {
            throw InvalidRequestException::create();
        }

        if (Arr::isAssoc($requestObjects)) {
            return RequestData::from([
                'requestObjects' => [$requestObjects],
                'isBatch' => false,
            ]);
        }

        return RequestData::from([
            'requestObjects' => $requestObjects,
            'isBatch' => true,
        ]);
    }

    private static function validate(mixed $data): void
    {
        if (!is_array($data)) {
            throw InvalidRequestException::create();
        }

        $validator = Validator::make(
            $data,
            [
                'jsonrpc' => ['required', 'in:2.0'],
                'id' => new Identifier(),
                'method' => ['required', 'string'],
                'params' => ['nullable', 'array'],
            ],
        );

        if ($validator->fails()) {
            throw InvalidRequestException::createFromValidator($validator);
        }
    }
}
