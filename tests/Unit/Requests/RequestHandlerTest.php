<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\JsonRpc\Contracts\ServerInterface;
use Cline\JsonRpc\Requests\RequestHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Tests\Support\Fakes\Server;

beforeEach(function (): void {
    Route::rpc(Server::class);

    App::bind(ServerInterface::class, Server::class);
});

it('can call a method from an array', function (): void {
    $result = RequestHandler::createFromArray([
        'jsonrpc' => '2.0',
        'id' => 1,
        'method' => 'app.subtract_with_binding',
        'params' => [
            'data' => ['subtrahend' => 23, 'minuend' => 42],
        ],
    ]);

    expect($result->toArray())->toBe([
        'data' => [
            'jsonrpc' => '2.0',
            'id' => 1,
            'result' => 19,
        ],
        'statusCode' => 200,
        'headers' => [],
    ]);
});

it('can call a method from a string', function (): void {
    $result = RequestHandler::createFromString(
        json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'app.subtract_with_binding',
            'params' => [
                'data' => ['subtrahend' => 23, 'minuend' => 42],
            ],
        ]),
    );

    expect($result->toArray())->toBe([
        'data' => [
            'jsonrpc' => '2.0',
            'id' => 1,
            'result' => 19,
        ],
        'statusCode' => 200,
        'headers' => [],
    ]);
});
