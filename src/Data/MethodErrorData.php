<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Data;

use Override;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class MethodErrorData extends AbstractData
{
    public function __construct(
        public readonly string $jsonrpc,
        public readonly mixed $id,
        public readonly ErrorData $error,
    ) {}

    #[Override()]
    public function toArray(): array
    {
        return [
            'jsonrpc' => $this->jsonrpc,
            'id' => $this->id,
            'error' => $this->error,
        ];
    }
}
