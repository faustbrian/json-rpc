<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions;

use Illuminate\Support\Arr;

use function array_diff;
use function implode;
use function sprintf;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidSortsException extends AbstractRequestException
{
    public static function create(array $unknownSorts, array $allowedSorts): self
    {
        $unknownSorts = Arr::pluck($unknownSorts, 'attribute');
        $unknownSorts = implode(', ', array_diff($unknownSorts, $allowedSorts));

        $allowedSorts = implode(', ', $allowedSorts);

        return self::new(-32_602, 'Invalid params', [
            [
                'status' => '422',
                'source' => ['pointer' => '/params/sorts'],
                'title' => 'Invalid sorts',
                'detail' => sprintf('Requested sorts `%s` is not allowed. Allowed sorts are `%s`.', $unknownSorts, $allowedSorts),
                'meta' => [
                    'unknown' => $unknownSorts,
                    'allowed' => $allowedSorts,
                ],
            ],
        ]);
    }
}
