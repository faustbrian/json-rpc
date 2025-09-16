<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ExceptionMapper
{
    public static function execute(Throwable $exception): AbstractRequestException
    {
        return match (true) {
            $exception instanceof AbstractRequestException => $exception,
            $exception instanceof AuthenticationException => UnauthorizedException::create(),
            $exception instanceof AuthorizationException => ForbiddenException::create(),
            $exception instanceof ItemNotFoundException => ResourceNotFoundException::create(),
            $exception instanceof ModelNotFoundException => ResourceNotFoundException::create(),
            $exception instanceof ThrottleRequestsException => TooManyRequestsException::create(),
            $exception instanceof ValidationException => UnprocessableEntityException::createFromValidationException($exception),
            default => InternalErrorException::create($exception),
        };
    }
}
