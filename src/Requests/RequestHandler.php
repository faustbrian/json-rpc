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
use function throw_if;
use function throw_unless;

/**
 * Processes JSON-RPC 2.0 requests and dispatches them to registered methods.
 *
 * This handler parses incoming JSON-RPC requests, validates their structure,
 * routes them to the appropriate method handlers, and constructs standardized
 * responses. Supports both single requests and batch requests, handles notifications,
 * and provides comprehensive error handling with proper HTTP status codes.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class RequestHandler
{
    /**
     * Creates a handler result from an array-based request.
     *
     * @param  array<string, mixed> $request Parsed JSON-RPC request data
     * @return RequestResultData    Result containing response data and HTTP status code
     */
    public static function createFromArray(array $request): RequestResultData
    {
        return new self()->handle($request);
    }

    /**
     * Creates a handler result from a JSON string request.
     *
     * @param  string            $request Raw JSON-RPC request string
     * @return RequestResultData Result containing response data and HTTP status code
     */
    public static function createFromString(string $request): RequestResultData
    {
        return new self()->handle($request);
    }

    /**
     * Processes a JSON-RPC request and returns the result.
     *
     * Handles the complete request lifecycle: parsing, validation, method dispatch,
     * and response construction. Supports both single and batch requests, properly
     * handles notifications (no response), and maps exceptions to appropriate
     * JSON-RPC error responses with correct HTTP status codes.
     *
     * @param array<string, mixed>|string $request JSON-RPC request as array or JSON string
     *
     * @throws InvalidRequestException When the request structure is invalid
     * @throws ParseErrorException     When the JSON cannot be decoded
     *
     * @return RequestResultData Result containing response data and HTTP status code
     */
    public function handle(array|string $request): RequestResultData
    {
        try {
            $requestBody = self::parse($request);

            throw_if(count($requestBody->requestObjects) > 10, InvalidRequestException::create([
                [
                    'status' => '400',
                    'source' => ['pointer' => '/'],
                    'title' => 'Invalid request',
                    'detail' => 'The request contains too many items. The maximum is 10.',
                ],
            ]));

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

    /**
     * Parses and normalizes the request into a RequestData object.
     *
     * Decodes JSON strings, validates the structure, and determines whether the
     * request is a single request or batch request based on array structure.
     *
     * @param array<string, mixed>|string $requestObjects Raw request data as array or JSON string
     *
     * @throws InvalidRequestException When the request structure is invalid or empty
     * @throws ParseErrorException     When JSON decoding fails
     *
     * @return RequestData Normalized request data with batch flag
     */
    private static function parse(array|string $requestObjects): RequestData
    {
        if (is_string($requestObjects)) {
            try {
                $requestObjects = json_decode($requestObjects, true, 512, JSON_THROW_ON_ERROR);
            } catch (Throwable) {
                throw ParseErrorException::create();
            }
        }

        throw_if(empty($requestObjects), InvalidRequestException::create());

        throw_unless(is_array($requestObjects), InvalidRequestException::create());

        // Single request if array is associative, batch if numeric
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

    /**
     * Validates a request object against JSON-RPC 2.0 specification.
     *
     * Ensures the request contains required fields (jsonrpc, method) with correct
     * types and values. The id field is optional (notifications omit it).
     *
     * @param mixed $data Request object to validate
     *
     * @throws InvalidRequestException When validation fails
     */
    private static function validate(mixed $data): void
    {
        throw_unless(is_array($data), InvalidRequestException::create());

        $validator = Validator::make(
            $data,
            [
                'jsonrpc' => ['required', 'in:2.0'],
                'id' => new Identifier(),
                'method' => ['required', 'string'],
                'params' => ['nullable', 'array'],
            ],
        );

        throw_if($validator->fails(), InvalidRequestException::createFromValidator($validator));
    }
}
