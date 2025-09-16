<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Data\Configuration;

use Cline\JsonRpc\Data\AbstractData;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ServerData extends AbstractData
{
    public function __construct(
        public readonly string $name,
        public readonly string $path,
        public readonly string $route,
        public readonly string $version,
        public readonly array $middleware,
        public readonly ?array $methods,
        public readonly array $content_descriptors,
        public readonly array $schemas,
    ) {}
}
