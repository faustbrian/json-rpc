<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Methods;

use Cline\JsonRpc\Data\DocumentData;
use Cline\OpenRpc\ContentDescriptor\CursorPaginatorContentDescriptor;
use Cline\OpenRpc\ContentDescriptor\FieldsContentDescriptor;
use Cline\OpenRpc\ContentDescriptor\FiltersContentDescriptor;
use Cline\OpenRpc\ContentDescriptor\RelationshipsContentDescriptor;
use Cline\OpenRpc\ContentDescriptor\SortsContentDescriptor;
use Override;

/**
 * @author Brian Faust <brian@cline.sh>
 */
abstract class AbstractListMethod extends AbstractMethod
{
    public function handle(): DocumentData
    {
        return $this->cursorPaginate(
            $this->query(
                $this->getResourceClass(),
            ),
        );
    }

    #[Override()]
    public function getParams(): array
    {
        $className = $this->getResourceClass();

        return [
            CursorPaginatorContentDescriptor::create(),
            FieldsContentDescriptor::create($className::getFields()),
            FiltersContentDescriptor::create($className::getFilters()),
            RelationshipsContentDescriptor::create($className::getRelationships()),
            SortsContentDescriptor::create($className::getSorts()),
        ];
    }

    abstract protected function getResourceClass(): string;
}
