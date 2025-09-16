<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;

use function is_numeric;
use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class Identifier implements ValidationRule
{
    #[Override()]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null) {
            return;
        }

        if (is_numeric($value)) {
            return;
        }

        if (is_string($value)) {
            return;
        }

        $fail('The :attribute must be an integer, string or null.');
    }
}
