<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Data;

use function in_array;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ErrorData extends AbstractData
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly mixed $data = null,
    ) {}

    /**
     * Determine if the response indicates a client error occurred.
     */
    public function isClient(): bool
    {
        return in_array($this->code, [
            -32_600, // Invalid Request
            -32_601, // Method not found
            -32_602, // Invalid params
        ], true);
    }

    /**
     * Determine if the response indicates a server error occurred.
     */
    public function isServer(): bool
    {
        // Invalid JSON was received by the server.
        if ($this->code === -32_700) {
            return true;
        }

        // Internal JSON-RPC error.
        if ($this->code === -32_603) {
            return true;
        }

        // Reserved for implementation-defined server-errors.
        return $this->code >= -32_099 && $this->code <= -32_000;
    }

    /**
     * @see https://www.jsonrpc.org/historical/json-rpc-over-http.html#id19
     */
    public function toStatusCode(): int
    {
        return match (true) {
            $this->code === -32_700 => 500, // Parse error.
            $this->code === -32_600 => 400, // Invalid Request.
            $this->code === -32_601 => 404, // Method not found.
            $this->code === -32_602 => 500, // Invalid params.
            $this->code === -32_603 => 500, // Internal error.
            $this->isServer() => 500,
            $this->isClient() => 400,
            default => 200,
        };
    }
}
