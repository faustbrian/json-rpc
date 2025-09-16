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
final class UnauthorizedException extends AbstractRequestException
{
    public static function create(?string $detail = null): self
    {
        return self::new(-32_000, 'Server error', [
            [
                'status' => '401',
                'title' => 'Unauthorized',
                'detail' => $detail ?? 'You are not authorized to perform this action.',
            ],
        ]);
    }

    #[Override()]
    public function getStatusCode(): int
    {
        return 401;
    }
}
