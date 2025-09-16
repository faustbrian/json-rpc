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
final class InvalidFieldsException extends AbstractRequestException
{
    public static function create(array $unknownFields, array $allowedFields): self
    {
        $unknownFields = implode(', ', array_diff($unknownFields, $allowedFields));
        $allowedFields = implode(', ', $allowedFields);

        return self::new(-32_602, 'Invalid params', [
            [
                'status' => '422',
                'source' => ['pointer' => '/params/fields'],
                'title' => 'Invalid fields',
                'detail' => sprintf('Requested fields `%s` are not allowed. Allowed fields are `%s`.', $unknownFields, $allowedFields),
                'meta' => [
                    'unknown' => $unknownFields,
                    'allowed' => $allowedFields,
                ],
            ],
        ]);
    }
}
