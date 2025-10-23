<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Support\Fakes;

use Cline\RPC\Exceptions\AbstractRequestException;
use Override;

/**
 * Fake exception class for testing custom status codes in exception responses.
 *
 * @internal
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class CustomStatusCodeException extends AbstractRequestException
{
    #[Override()]
    public function getStatusCode(): int
    {
        return 404;
    }
}
