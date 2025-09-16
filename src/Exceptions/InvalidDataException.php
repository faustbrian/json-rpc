<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions;

use Illuminate\Validation\ValidationException;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidDataException extends AbstractRequestException
{
    public static function create(ValidationException $exception): self
    {
        $normalized = [];

        foreach ($exception->errors() as $attribute => $errors) {
            foreach ($errors as $error) {
                $normalized[] = [
                    'status' => '422',
                    'source' => ['pointer' => '/params/data/'.$attribute],
                    'title' => 'Invalid params',
                    'detail' => $error,
                ];
            }
        }

        return self::new(-32_602, 'Invalid params', $normalized);
    }
}
