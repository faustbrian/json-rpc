<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions;

use Illuminate\Validation\Validator;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidRequestException extends AbstractRequestException
{
    public static function create(?array $data = null): self
    {
        return self::new(-32_600, 'Invalid Request', $data);
    }

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
