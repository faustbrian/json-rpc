<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions;

use function array_diff;
use function implode;
use function sprintf;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidFiltersException extends AbstractRequestException
{
    public static function create(array $unknownFilters, array $allowedFilters): self
    {
        $unknownFilters = implode(', ', array_diff($unknownFilters, $allowedFilters));
        $allowedFilters = implode(', ', $allowedFilters);

        return self::new(-32_602, 'Invalid params', [
            [
                'status' => '422',
                'source' => ['pointer' => '/params/filters'],
                'title' => 'Invalid filters',
                'detail' => sprintf('Requested filters `%s` are not allowed. Allowed filters are `%s`.', $unknownFilters, $allowedFilters),
                'meta' => [
                    'unknown' => $unknownFilters,
                    'allowed' => $allowedFilters,
                ],
            ],
        ]);
    }
}
