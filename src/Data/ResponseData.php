<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Data;

use Cline\JsonRpc\Exceptions\AbstractRequestException;
use Override;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ResponseData extends AbstractData
{
    public function __construct(
        public readonly string $jsonrpc,
        public readonly mixed $id = null,
        public readonly mixed $result = null,
        public readonly ?ErrorData $error = null,
    ) {}

    public static function createFromRequestException(AbstractRequestException $exception): self
    {
        return self::from([
            'jsonrpc' => '2.0',
            'error' => $exception->toError(),
        ]);
    }

    public static function asNotification(): self
    {
        return self::from([
            'jsonrpc' => '2.0',
        ]);
    }

    /**
     * Determine if the request was successful.
     */
    public function isSuccessful(): bool
    {
        return !$this->isFailed();
    }

    /**
     * Determine if the response indicates a client or server error occurred.
     */
    public function isFailed(): bool
    {
        if ($this->isServerError()) {
            return true;
        }

        return $this->isClientError();
    }

    /**
     * Determine if the response indicates a client error occurred.
     */
    public function isClientError(): bool
    {
        if (!$this->error instanceof ErrorData) {
            return false;
        }

        return $this->error->isClient();
    }

    /**
     * Determine if the response indicates a server error occurred.
     */
    public function isServerError(): bool
    {
        if (!$this->error instanceof ErrorData) {
            return false;
        }

        return $this->error->isServer();
    }

    /**
     * Determine if the request is a notification.
     */
    public function isNotification(): bool
    {
        return $this->id === null && $this->result === null && !$this->error instanceof ErrorData;
    }

    #[Override()]
    public function toArray(): array
    {
        if (!$this->error instanceof ErrorData) {
            return [
                'jsonrpc' => $this->jsonrpc,
                'id' => $this->id,
                'result' => $this->result,
            ];
        }

        return [
            'jsonrpc' => $this->jsonrpc,
            'id' => $this->id,
            'error' => $this->error,
        ];
    }
}
