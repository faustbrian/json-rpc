<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Support\Resources;

use Cline\RPC\Resources\AbstractModelResource;
use Override;

/**
 * Test resource with relation names that conflict with attributes.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class ResourceWithRelationConflict extends AbstractModelResource
{
    private array $relations = [];

    #[Override()]
    public static function getFields(): array
    {
        return ['self' => ['id', 'name', 'posts']];
    }

    #[Override()]
    public static function getFilters(): array
    {
        return ['self' => []];
    }

    #[Override()]
    public static function getRelationships(): array
    {
        return ['self' => []];
    }

    #[Override()]
    public static function getSorts(): array
    {
        return ['self' => []];
    }

    public function setRelations(array $relations): void
    {
        $this->relations = $relations;
    }

    #[Override()]
    public function getRelations(): array
    {
        return $this->relations;
    }
}
