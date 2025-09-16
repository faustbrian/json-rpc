<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ForceJson
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->is('rpc') || $request->is('rpc/*')) {
            $request->headers->set('Content-Type', 'application/json');
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
