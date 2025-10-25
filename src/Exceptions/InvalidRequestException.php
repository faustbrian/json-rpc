<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\RPC\Exceptions;

use Illuminate\Validation\Validator;

/**
 * Exception thrown when a JSON-RPC request is malformed or structurally invalid.
 *
 * Represents JSON-RPC error code -32600 for requests that fail basic structural
 * validation, such as missing required members (jsonrpc, method, id), invalid
 * JSON-RPC version, or malformed request structure. This is distinct from parameter
 * validation errors which use error code -32602.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidRequestException extends AbstractRequestException
{
    /**
     * Creates an invalid request exception with optional error details.
     *
     * Generates a JSON-RPC compliant error response for structural request validation
     * failures. Used for requests that don't meet basic JSON-RPC specification
     * requirements rather than method-specific parameter validation issues.
     *
     * @param  null|array<int, array<string, mixed>> $data optional array of error details
     *                                                     following JSON:API error object
     *                                                     structure with status, source pointer,
     *                                                     title, and detail fields for describing
     *                                                     specific structural violations
     * @return self                                  a new instance with JSON-RPC error code -32600 (Invalid Request)
     *                                               and the provided error details, or null data for generic request
     *                                               structure validation failures
     */
    public static function create(?array $data = null): self
    {
        return self::new(-32_600, 'Invalid Request', $data);
    }

    /**
     * Creates an invalid request exception from a Laravel validator instance.
     *
     * Transforms Laravel validation errors for JSON-RPC request structure into a
     * JSON-RPC compliant error response. Each validation error is converted into
     * an error object with JSON Pointer notation indicating the exact location of
     * the structural violation in the request document.
     *
     * @param  Validator $validator Laravel validator instance containing validation errors
     *                              for the JSON-RPC request structure. Errors are typically
     *                              for missing or invalid top-level request members like
     *                              'jsonrpc', 'method', 'id', or 'params' rather than the
     *                              method parameter validation handled by other exceptions.
     * @return self      a new instance containing all validation errors formatted as JSON-RPC
     *                   error objects, each with HTTP 422 status, JSON Pointer source location
     *                   (/{attribute}), and the specific validation failure message
     */
    public static function createFromValidator(Validator $validator): self
    {
        $normalized = [];

        foreach ($validator->errors()->messages() as $attribute => $errors) {
            foreach ($errors as $error) {
                $normalized[] = [
                    'status' => '422',
                    'source' => ['pointer' => '/'.$attribute],
                    'title' => 'Invalid member',
                    'detail' => $error,
                ];
            }
        }

        return self::create($normalized);
    }
}
