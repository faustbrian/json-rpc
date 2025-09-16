<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions\Actions;

use Cline\JsonRpc\Exceptions\ExceptionMapper;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Throwable;

use function array_filter;
use function response;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class RendersThrowable
{
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
