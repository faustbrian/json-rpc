<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Repositories;

use Cline\JsonRpc\Contracts\ResourceInterface;
use Cline\JsonRpc\Exceptions\InternalErrorException;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use function sprintf;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ResourceRepository
{
    /** @var array<string, string> */
    private static array $resources = [];

    public static function all(): array
    {
        return self::$resources;
    }

    public static function get(Model $model): ResourceInterface
    {
        $resource = self::$resources[$model::class] ?? null;

        if ($resource === null) {
            throw InternalErrorException::create(
                new DomainException(sprintf('Resource for model [%s] not found.', $model)),
            );
        }

        $resource = new $resource($model);

        if ($resource instanceof ResourceInterface) {
            return $resource;
        }

        throw InternalErrorException::create(
            new DomainException(sprintf('Resource for model [%s] not found.', $model)),
        );
    }

    public static function forget(string $model): void
    {
        Arr::forget(self::$resources, $model);
    }

    public static function register(string $model, string $resource): void
    {
        self::$resources[$model] = $resource;
    }
}
