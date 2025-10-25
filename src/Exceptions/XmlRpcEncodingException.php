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
 * Exception thrown when XML-RPC encoding fails.
 *
 * This exception is thrown when the XML-RPC protocol implementation encounters
 * an error while encoding a request or response to XML format. This typically
 * occurs due to invalid data structures or XML generation failures.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class XmlRpcEncodingException extends RuntimeException
{
    /**
     * Create exception for request encoding failure.
     *
     * @param  Throwable $previous The underlying exception that caused the encoding failure
     * @return self      The created exception instance
     */
    public static function request(Throwable $previous): self
    {
        return new self('XML-RPC request encoding failed', 0, $previous);
    }

    /**
     * Create exception for response encoding failure.
     *
     * @param  Throwable $previous The underlying exception that caused the encoding failure
     * @return self      The created exception instance
     */
    public static function response(Throwable $previous): self
    {
        return new self('XML-RPC response encoding failed', 0, $previous);
    }
}
