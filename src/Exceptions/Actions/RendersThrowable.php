<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\RPC\Exceptions\Actions;

use Cline\RPC\Exceptions\ExceptionMapper;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Throwable;

use function array_filter;
use function response;

/**
 * Action class for registering JSON-RPC error renderers.
 *
 * This action configures Laravel's exception handler to render exceptions as
 * JSON-RPC 2.0 compliant error responses. Automatically transforms standard
 * Laravel exceptions into appropriate JSON-RPC error formats when the request
 * expects JSON responses.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class RendersThrowable
{
    /**
     * Register the JSON-RPC exception renderer.
     *
     * Configures the Laravel exception handler to intercept exceptions and render
     * them as JSON-RPC 2.0 error responses for JSON requests. The renderer maps
     * Laravel exceptions to JSON-RPC exception types, formats them with proper
     * error codes and messages, and returns appropriate HTTP status codes.
     *
     * @param Exceptions $exceptions The Laravel exception configuration instance to register
     *                               the renderer with. Modified to include JSON-RPC error
     *                               rendering logic for JSON API requests.
     */
    public static function execute(Exceptions $exceptions): void
    {
        $exceptions->renderable(
            function (Throwable $exception, Request $request) {
                if (!$request->wantsJson()) {
                    return;
                }

                $exception = ExceptionMapper::execute($exception);

                return response()->json(
                    array_filter([
                        'jsonrpc' => '2.0',
                        'id' => $request->input('id'),
                        'error' => $exception->toArray(),
                    ]),
                    $exception->getStatusCode(),
                    $exception->getHeaders(),
                );
            },
        );
    }
}
