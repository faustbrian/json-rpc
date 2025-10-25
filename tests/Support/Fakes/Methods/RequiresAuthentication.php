<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Support\Fakes\Methods;

use Cline\RPC\Methods\AbstractMethod;
use Illuminate\Auth\AuthenticationException;

/**
 * Test method that always throws an AuthenticationException.
 * Used to test authentication error handling in RequestHandler.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class RequiresAuthentication extends AbstractMethod
{
    public function handle(): never
    {
        throw new AuthenticationException('User not authenticated');
    }
}
