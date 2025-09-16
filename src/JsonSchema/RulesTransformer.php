<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\JsonSchema;

use function array_merge;
use function explode;
use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class RulesTransformer
{
    public static function transform(array $rules, array $properties = []): array
    {
        $schema = [
            'type' => 'object',
            'properties' => [],
            'required' => [],
        ];

        foreach ($rules as $field => $fieldRules) {
            $parsedRules = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            $fieldSchema = RuleTransformer::transform($field, $parsedRules);

            if ($fieldSchema !== []) {
                if (!empty($fieldSchema['required'])) {
                    $schema['required'][] = $field;

                    if ($fieldSchema['type'] !== 'object') {
                        unset($fieldSchema['required']);
                    }
                }

                $schema['properties'][$field] = $fieldSchema;
            }
        }

        foreach ($properties as $field => $fieldSchema) {
            $schema['properties'][$field] = array_merge($schema['properties'][$field] ?? [], $fieldSchema);
        }

        return $schema;
    }

    public static function transformDataObject(string $data, array $properties = []): array
    {
        return self::transform($data::getValidationRules([]), $properties);
    }
}
