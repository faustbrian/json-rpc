<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ServerErrorException extends AbstractRequestException
{
    public static function create(?array $data = null): self
    {
        return self::new(-32_000, 'Server error', $data);
    }
}
