<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Data;

use Cline\OpenRpc\ContentDescriptor\MethodDataContentDescriptor;

/**
 * @author Brian Faust <brian@cline.sh>
 */
abstract class AbstractContentDescriptorData extends AbstractData
{
    public static function createContentDescriptor(): array
    {
        return MethodDataContentDescriptor::createFromData(self::class);
    }

    protected static function defaultContentDescriptors(): array
    {
        return [];
    }
}
