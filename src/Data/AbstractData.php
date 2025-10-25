<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\RPC\Data;

use Override;
use Spatie\LaravelData\Data;

use function is_array;

/**
 * Base data transfer object with automatic null value filtering.
 *
 * Extends Spatie's Laravel Data package to provide automatic removal of null
 * values during serialization. This ensures compliance with JSON:API and
 * JSON-RPC specifications which require optional fields to be omitted rather
 * than explicitly set to null.
 *
 * All data objects in the JSON-RPC package should extend this class to
 * maintain consistent serialization behavior across the application.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class AbstractData extends Data
{
    /**
     * Convert the data object to an array with null values removed.
     *
     * Overrides the parent toArray method to automatically filter out null
     * values at all nesting levels. This ensures compliance with JSON:API
     * and JSON-RPC specifications.
     *
     * @return array<string, mixed> Array representation without null values
     */
    #[Override()]
    public function toArray(): array
    {
        /** @var array<string, mixed> $array */
        $array = parent::toArray();

        return $this->removeNullValuesRecursively($array);
    }

    /**
     * Prepare the data for JSON serialization.
     *
     * Ensures consistent JSON encoding by delegating to toArray, which
     * automatically handles null value filtering.
     *
     * @return array<string, mixed> Array ready for JSON encoding
     */
    #[Override()]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Recursively remove null values from nested arrays.
     *
     * Traverses the array structure and removes any keys with null values.
     * This is required for JSON:API and JSON-RPC specification compliance
     * where optional fields must be omitted entirely rather than set to null.
     *
     * According to JSON:API specification: "Keys MUST either be omitted or
     * have a null value to indicate that a particular link is unavailable."
     *
     * @param  array<string, mixed> $array Input array potentially containing null values
     * @return array<string, mixed> Filtered array with null values removed
     */
    private function removeNullValuesRecursively(array $array): array
    {
        foreach ($array as $key => $value) {
            if ($value === null) {
                unset($array[$key]);

                continue;
            }

            if (is_array($value)) {
                /** @var array<string, mixed> $recursiveValue */
                $recursiveValue = $value;
                $array[$key] = $this->removeNullValuesRecursively($recursiveValue);
            }
        }

        return $array;
    }
}
