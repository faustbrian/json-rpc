<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions;

use Override;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ServiceUnavailableException extends AbstractRequestException
{
    public static function create(?string $detail = null): self
    {
        return self::new(-32_000, 'Server error', [
            [
                'status' => '503',
                'title' => 'Service Unavailable',
                'detail' => $detail ?? 'The server is currently unable to handle the request due to a temporary overload or scheduled maintenance, which will likely be alleviated after some delay.',
            ],
        ]);
    }

    #[Override()]
    public function getStatusCode(): int
    {
        return 503;
    }
}
