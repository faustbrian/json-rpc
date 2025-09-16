<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions;

use Illuminate\Validation\ValidationException;
use Override;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class UnprocessableEntityException extends AbstractRequestException
{
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

    #[Override()]
    public function getStatusCode(): int
    {
        return 422;
    }
}
