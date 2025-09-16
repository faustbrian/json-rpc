<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc;

use function array_filter;
use function function_exists;
use function route;

if (!function_exists('post_json_rpc') && function_exists('Pest\Laravel\postJson')) {
    function post_json_rpc(string $method, ?array $params = null, ?string $id = null): \Illuminate\Testing\TestResponse
    {
        return \Pest\Laravel\postJson(
            route('rpc'),
            array_filter([
                'jsonrpc' => '2.0',
                'id' => $id ?? '01J34641TE5SF58ZX3N9HPT1BA',
                'method' => $method,
                'params' => $params,
            ]),
        );
    }
}
