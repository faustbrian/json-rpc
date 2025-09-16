<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\JsonRpc\Contracts\ServerInterface;
use Cline\JsonRpc\Http\Middleware\BootServer;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Fakes\Server;

use function Pest\Laravel\post;

beforeEach(function (): void {
    Route::rpc(Server::class);
});

it('binds ServerInterface to the container', function (): void {
    post(route('rpc'));

    expect(App::get(ServerInterface::class))->toBeInstanceOf(ServerInterface::class);
});

// it('removes ServerInterface from the container after request', function (): void {
//     $middleware = App::make(BootServer::class);

//     $request = Request::create('/rpc', 'GET', [], [], [], []);
//     $response = $middleware->handle($request, fn () => new Response());
//     $middleware->terminate($request, $response);

//     App::get(ServerInterface::class);
// })->throws(EntryNotFoundException::class);
