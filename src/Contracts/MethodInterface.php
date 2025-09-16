<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Contracts;

use Cline\JsonRpc\Data\RequestObjectData;
use Cline\OpenRpc\ValueObject\ContentDescriptorValue;

/**
 * @author Brian Faust <brian@cline.sh>
 */
interface MethodInterface
{
    public function getName(): string;

    public function getSummary(): string;

    public function getParams(): array;

    public function getResult(): ?ContentDescriptorValue;

    public function getErrors(): array;

    public function setRequest(RequestObjectData $requestObject): void;
}
