<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Mixins;

use Cline\JsonRpc\Contracts\ServerInterface;
use Cline\JsonRpc\Http\Controllers\MethodController;
use Cline\JsonRpc\Repositories\ServerRepository;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class RouteMixin
{
    public function rpc(): Closure
    {
        /**
         * @param class-string<ServerInterface> $server
         */
        return function (string|ServerInterface $server): void {
            if (is_string($server)) {
                /** @var ServerInterface $server */
                $server = App::make($server);
            }

            App::make(ServerRepository::class)->register($server);

            Route::post($server->getRoutePath(), MethodController::class)
                ->name($server->getRouteName())
                ->middleware($server->getMiddleware());
        };
    }
}
