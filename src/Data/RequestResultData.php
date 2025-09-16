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
final class RequestResultData extends AbstractData
{
    public function __construct(
        public readonly mixed $data,
        public readonly int $statusCode,
        public readonly array $headers = [],
    ) {}
}
