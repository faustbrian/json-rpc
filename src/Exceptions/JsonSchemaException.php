<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Exceptions;

use function sprintf;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class JsonSchemaException extends AbstractRequestException
{
    public static function invalidRule(string $rule): self
    {
        return self::new(-32_603, 'Internal error', [
            [
                'status' => '418',
                'source' => ['pointer' => '/'],
                'title' => 'Invalid JSON Schema',
                'detail' => sprintf("The '%s' rule is not supported.", $rule),
            ],
        ]);
    }
}
