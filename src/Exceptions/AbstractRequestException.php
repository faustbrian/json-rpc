<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions;

use Cline\JsonRpc\Data\ErrorData;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

use function array_filter;

/**
 * @author Brian Faust <brian@cline.sh>
 */
abstract class AbstractRequestException extends Exception
{
    public function __construct(
        public readonly ErrorData $error,
    ) {
        parent::__construct(
            $this->getErrorMessage(),
            $this->getErrorCode(),
        );
    }

    public function getErrorCode(): int
    {
        return $this->error->code ?? $this->code;
    }

    public function getErrorMessage(): string
    {
        return $this->error->message ?? $this->message;
    }

    public function getErrorData(): mixed
    {
        return $this->error->data;
    }

    public function getStatusCode(): int
    {
        return match ($this->getErrorCode()) {
            -32_700 => 400,
            -32_600 => 400,
            -32_601 => 404,
            -32_602 => 400,
            -32_603 => 500,
            default => 500,
        };
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function toError(): ErrorData
    {
        return ErrorData::from($this->toArray());
    }

    public function toArray(): array
    {
        $message = [
            'code' => $this->getErrorCode(),
            'message' => $this->getErrorMessage(),
            'data' => $this->getErrorData(),
        ];

        if (App::hasDebugModeEnabled()) {
            Arr::set(
                $message,
                'data.debug',
                [
                    'file' => $this->getFile(),
                    'line' => $this->getLine(),
                    'trace' => $this->getTraceAsString(),
                ],
            );
        }

        return array_filter($message);
    }

    protected static function new(?int $code, ?string $message, ?array $data = null): static
    {
        // @phpstan-ignore-next-line
        return new static(
            ErrorData::from([
                'code' => $code,
                'message' => $message,
                'data' => $data,
            ]),
        );
    }
}
