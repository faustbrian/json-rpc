<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\RPC\Http\Controllers;

use Cline\RPC\Contracts\ProtocolInterface;
use Cline\RPC\Requests\RequestHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Spatie\LaravelData\Data;

/**
 * HTTP controller for handling JSON-RPC method invocations.
 *
 * This controller serves as the entry point for all JSON-RPC requests, receiving
 * HTTP requests, delegating processing to the RequestHandler, and formatting the
 * response according to JSON-RPC 2.0 specifications. It acts as a bridge between
 * Laravel's HTTP layer and the JSON-RPC protocol layer.
 *
 * The controller follows JSON-RPC-over-HTTP conventions where HTTP status codes
 * reflect transport-level concerns rather than application-level RPC errors.
 * RPC errors are encoded in the JSON-RPC response body with HTTP 200 status.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class MethodController
{
    /**
     * Handle the incoming RPC request.
     *
     * Processes RPC requests by extracting the raw request body, passing it
     * to the RequestHandler for parsing and method execution, and formatting the
     * result according to the protocol (JSON-RPC, XML-RPC, etc.). The method
     * supports both single requests and batch requests as per JSON-RPC 2.0 specification.
     *
     * HTTP Status Code Usage:
     * - 200 OK: Standard response for successful RPC responses, even when
     *   the RPC itself contains an error object. The HTTP protocol serves
     *   as transport and doesn't reflect RPC-level success or failure.
     * - 400 Bad Request: Used when the HTTP request is malformed, such as when
     *   the request body is not valid.
     * - 500 Internal Server Error: Used for server infrastructure failures that
     *   are not related to the RPC protocol itself.
     *
     * @param  Request           $request        The incoming HTTP request containing the RPC
     *                                           request payload in the body. The request content
     *                                           should be valid according to the protocol.
     * @param  RequestHandler    $requestHandler The request handler that parses RPC
     *                                           requests, routes them to appropriate
     *                                           methods, and formats responses. Injected
     *                                           via Laravel's service container.
     * @param  ProtocolInterface $protocol       The protocol for encoding responses. Injected
     *                                           via Laravel's service container.
     * @return HttpResponse|JsonResponse The RPC response formatted according to the protocol.
     *                                   The response data type varies based on the handler result:
     *                                   Collection and Data objects are converted to arrays before
     *                                   serialization, while other types are returned as-is.
     */
    public function __invoke(Request $request, RequestHandler $requestHandler, ProtocolInterface $protocol): HttpResponse|JsonResponse
    {
        $result = $requestHandler->handle($request->getContent());

        // Convert Data objects to arrays
        $data = $result->data instanceof Data ? $result->data->toArray() : $result->data;

        // If the response is already encoded (from unwrapped responses), return as-is
        if ($result->encoded ?? false) {
            return Response::make($data, $result->statusCode, [
                'Content-Type' => $protocol->getContentType(),
            ]);
        }

        // For standard responses, use protocol encoding
        return Response::json($data, $result->statusCode);
    }
}
