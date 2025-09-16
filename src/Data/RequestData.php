<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Data;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class RequestData extends AbstractData
{
    public function __construct(
        public readonly array $requestObjects,
        public readonly bool $isBatch,
    ) {}
}
