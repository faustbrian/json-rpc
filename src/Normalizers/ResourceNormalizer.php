<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Normalizers;

use Cline\JsonRpc\Contracts\ResourceInterface;
use Cline\JsonRpc\Data\ResourceObjectData;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class ResourceNormalizer
{
    public static function normalize(ResourceInterface $resource): ResourceObjectData
    {
        $pendingResourceObject = $resource->toArray();

        foreach ($resource->getRelations() as $relationName => $relationModels) {
            $isOneToOne = Str::plural($relationName) !== $relationName;

            if ($isOneToOne) {
                $relationModels = Arr::wrap($relationModels);
            }

            /** @var ResourceInterface $relationship */
            foreach ($relationModels as $relationship) {
                if ($isOneToOne) {
                    $pendingResourceObject['relationships'][$relationName] = $relationship;
                } else {
                    $pendingResourceObject['relationships'][$relationName][] = $relationship;
                }
            }
        }

        return ResourceObjectData::from($pendingResourceObject);
    }
}
