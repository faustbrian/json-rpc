<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Data\Errors;

use Cline\JsonRpc\Data\AbstractData;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @see https://jsonapi.org/format/#error-objects
 */
final class SourceData extends AbstractData
{
    public function __construct(
        public readonly ?string $pointer,
        public readonly ?string $parameter,
        public readonly ?string $header,
    ) {}
}
