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
final class TooManyRequestsException extends AbstractRequestException
{
    public static function create(?string $detail = null): self
    {
        return self::new(-32_000, 'Server error', [
            [
                'status' => '429',
                'title' => 'Too Many Requests',
                'detail' => $detail ?? 'The server is refusing to service the request because the rate limit has been exceeded. Please wait and try again later.',
            ],
        ]);
    }

    #[Override()]
    public function getStatusCode(): int
    {
        return 429;
    }
}
