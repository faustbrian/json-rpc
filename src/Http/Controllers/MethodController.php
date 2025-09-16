<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Http\Controllers;

use Cline\JsonRpc\Requests\RequestHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Spatie\LaravelData\Data;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class MethodController
{
    /**
     * 200 OK: This should be the standard response for successful JSON-RPC
     * responses, even if the JSON-RPC itself contains an error object. The HTTP
     * protocol is just a transport in this case and doesn't need to reflect the
     * success or failure of the JSON-RPC command.
     *
     * 400 Bad Request: Use this status code if the HTTP request itself is
     * malformed. For instance, if the JSON-RPC request is not valid JSON.
     *
     * 500 Internal Server Error: This can be used for server-side errors that
     * are not related to the JSON-RPC protocol itself, such as a failure in the
     * server infrastructure.
     */
    public function __invoke(Request $request, RequestHandler $requestHandler): JsonResponse
    {
        $result = $requestHandler->handle($request->getContent());

        if ($result->data instanceof Collection) {
            return Response::json($result->data->toArray(), $result->statusCode);
        }

        if ($result->data instanceof Data) {
            return Response::json($result->data->toArray(), $result->statusCode);
        }

        return Response::json($result->data, $result->statusCode);
    }
}
