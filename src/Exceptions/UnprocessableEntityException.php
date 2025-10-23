<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\RPC\Exceptions;

use Illuminate\Validation\ValidationException;
use Override;

/**
 * Exception thrown when request validation fails.
 *
 * This exception represents a JSON-RPC server error that maps to HTTP 422,
 * indicating that the request is well-formed but contains semantic errors that
 * prevent processing. This is commonly used for validation failures where the
 * request structure is valid but the data doesn't meet business rules or
 * constraints. The exception formats validation errors according to JSON:API
 * error specifications with source pointers for precise error location.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class UnprocessableEntityException extends AbstractRequestException
{
    /**
     * Create a new unprocessable entity exception instance.
     *
     * @param  null|string $detail Optional detailed explanation of why the request
     *                             could not be processed (e.g., "Email format is invalid").
     *                             If not provided, uses a default message about semantic
     *                             errors. This detail is included in the JSON:API error response.
     * @return self        The created exception instance with JSON-RPC error code -32000
     */
    public static function create(?string $detail = null): self
    {
        return self::new(-32_000, 'Server error', [
            [
                'status' => '422',
                'title' => 'Unprocessable Entity',
                'detail' => $detail ?? 'The request was well-formed but was unable to be followed due to semantic errors.',
            ],
        ]);
    }

    /**
     * Create an unprocessable entity exception from a Laravel validation exception.
     *
     * Converts Laravel's ValidationException into a JSON-RPC error response with
     * JSON:API formatted error objects. Each validation error is transformed into
     * a separate error object with a source pointer indicating the parameter field
     * that failed validation, enabling precise client-side error handling.
     *
     * @param  ValidationException $exception The Laravel validation exception containing
     *                                        validation error messages organized by field name.
     *                                        Each field can have multiple error messages that
     *                                        are flattened into individual JSON:API error objects.
     * @return self                The created exception instance with normalized validation errors
     */
    public static function createFromValidationException(ValidationException $exception): self
    {
        $normalized = [];

        foreach ($exception->errors() as $attribute => $errors) {
            foreach ($errors as $error) {
                $normalized[] = [
                    'status' => '422',
                    'source' => ['pointer' => '/params/'.$attribute],
                    'title' => 'Invalid params',
                    'detail' => $error,
                ];
            }
        }

        return self::new(-32_000, 'Unprocessable Entity', $normalized);
    }

    /**
     * Get the HTTP status code for this exception.
     *
     * @return int HTTP 422 Unprocessable Entity status code
     */
    #[Override()]
    public function getStatusCode(): int
    {
        return 422;
    }
}
