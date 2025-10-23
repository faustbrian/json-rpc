<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\RPC\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Exception thrown when XML-RPC decoding fails.
 *
 * This exception is thrown when the XML-RPC protocol implementation encounters
 * an error while decoding XML to internal data structures. This typically
 * occurs due to malformed XML or unexpected XML structure.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class XmlRpcDecodingException extends RuntimeException
{
    /**
     * Create exception for request decoding failure.
     *
     * @param  Throwable $previous The underlying exception that caused the decoding failure
     * @return self      The created exception instance
     */
    public static function request(Throwable $previous): self
    {
        return new self('XML-RPC request decoding failed', 0, $previous);
    }

    /**
     * Create exception for response decoding failure.
     *
     * @param  Throwable $previous The underlying exception that caused the decoding failure
     * @return self      The created exception instance
     */
    public static function response(Throwable $previous): self
    {
        return new self('XML-RPC response decoding failed', 0, $previous);
    }
}
