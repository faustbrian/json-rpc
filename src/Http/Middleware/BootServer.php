<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Http\Middleware;

use Cline\JsonRpc\Contracts\ServerInterface;
use Cline\JsonRpc\Repositories\ServerRepository;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class BootServer
{
    public function __construct(
        private Container $container,
        private ServerRepository $serverRepository,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if ($routeName === null) {
            throw new BadRequestHttpException('A route name is required to boot the server.');
        }

        $this->container->instance(
            ServerInterface::class,
            $this->serverRepository->findByName($routeName),
        );

        return $next($request);
    }

    public function terminate(): void
    {
        if (App::runningUnitTests()) {
            return;
        }

        $this->container->forgetInstance(ServerInterface::class);
    }
}
