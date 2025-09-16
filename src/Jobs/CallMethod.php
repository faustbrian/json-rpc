<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Jobs;

use Cline\JsonRpc\Contracts\MethodInterface;
use Cline\JsonRpc\Contracts\UnwrappedResponseInterface;
use Cline\JsonRpc\Data\RequestObjectData;
use Cline\JsonRpc\Data\ResponseData;
use Cline\JsonRpc\Exceptions\ExceptionMapper;
use Cline\JsonRpc\Exceptions\InvalidDataException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use ReflectionClass;
use ReflectionNamedType;
use Spatie\LaravelData\Data;
use Throwable;

use function array_filter;
use function call_user_func;
use function count;
use function is_subclass_of;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class CallMethod
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        private MethodInterface $method,
        private RequestObjectData $requestObject,
    ) {}

    public function handle(): array|ResponseData
    {
        try {
            $this->method->setRequest($this->requestObject);

            $result = App::call(
                // @phpstan-ignore-next-line
                [$this->method, 'handle'],
                [
                    'requestObject' => $this->requestObject,
                    ...self::resolveParameters(
                        $this->method,
                        (array) $this->requestObject->getParam('data'),
                    ),
                ],
            );

            if ($this->method instanceof UnwrappedResponseInterface) {
                /** @var array $result */
                return $result;
            }

            return ResponseData::from([
                'jsonrpc' => $this->requestObject->jsonrpc,
                'id' => $this->requestObject->id,
                'result' => $result,
            ]);
        } catch (Throwable $throwable) {
            return ResponseData::from([
                'jsonrpc' => '2.0',
                'id' => $this->requestObject->id,
                'error' => ExceptionMapper::execute($throwable)->toError(),
            ]);
        }
    }

    private static function resolveParameters(MethodInterface $method, array $params): array
    {
        if (count($params) < 1) {
            return [];
        }

        $parameters = new ReflectionClass($method)->getMethod('handle')->getParameters();
        $parametersMapped = [];

        foreach ($parameters as $parameter) {
            $parameterName = $parameter->getName();

            // This is an internal parameter, we don't want to map it.
            if ($parameterName === 'requestObject') {
                continue;
            }

            $parameterType = $parameter->getType();

            if ($parameterType instanceof ReflectionNamedType) {
                $parameterType = $parameterType->getName();
            }

            $parameterValue = Arr::get($params, $parameterName) ?? Arr::get($params, Str::snake($parameterName, '.'));

            if (is_subclass_of((string) $parameterType, Data::class)) {
                try {
                    $parametersMapped[$parameterName] = call_user_func(
                        [(string) $parameterType, 'validateAndCreate'],
                        $parameter->getName() === 'data' ? $params : $parameterValue,
                    );
                } catch (ValidationException $exception) {
                    throw InvalidDataException::create($exception);
                }
            } elseif ($parameterType === 'array' && $parameter->getName() === 'data') {
                $parametersMapped[$parameterName] = $params;
            } else {
                $parametersMapped[$parameterName] = $parameterValue;
            }
        }

        return array_filter($parametersMapped);
    }
}
