<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Validation;

use Illuminate\Validation\ValidationException;

use function validator;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class Validator
{
    public static function validate(array $data, array $rules): void
    {
        $validator = validator($data, $rules);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->all());
        }
    }
}
