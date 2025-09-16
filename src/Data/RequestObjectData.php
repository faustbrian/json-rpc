<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Data;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class RequestObjectData extends AbstractData
{
    public function __construct(
        public readonly string $jsonrpc,
        public readonly mixed $id,
        public readonly string $method,
        public readonly ?array $params,
    ) {}

    public static function asRequest(string $method, ?array $params = null, mixed $id = null): self
    {
        return self::from([
            'jsonrpc' => '2.0',
            'id' => $id ?? Str::ulid(),
            'method' => $method,
            'params' => $params,
        ]);
    }

    public static function asNotification(string $method, ?array $params = null): self
    {
        return self::from([
            'jsonrpc' => '2.0',
            'id' => null,
            'method' => $method,
            'params' => $params,
        ]);
    }

    public function getParam(string $key, mixed $default = null): mixed
    {
        if ($this->params === null) {
            return $default;
        }

        return Arr::get($this->params, $key, $default);
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function isNotification(): bool
    {
        return $this->id === null;
    }
}
