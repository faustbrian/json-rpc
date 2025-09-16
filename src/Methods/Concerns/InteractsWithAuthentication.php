<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Methods\Concerns;

use Illuminate\Contracts\Auth\Authenticatable;

use function abort_unless;
use function auth;

/**
 * @author Brian Faust <brian@cline.sh>
 */
trait InteractsWithAuthentication
{
    protected function getCurrentUser(): Authenticatable
    {
        abort_unless(auth()->check(), 401, 'Unauthorized');

        /** @var Authenticatable */
        return auth()->user();
    }
}
