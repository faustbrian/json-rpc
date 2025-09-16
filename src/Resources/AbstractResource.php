<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Resources;

use Cline\JsonRpc\Contracts\ResourceInterface;
use Override;

/**
 * @author Brian Faust <brian@cline.sh>
 */
abstract class AbstractResource implements ResourceInterface
{
    public static function getFields(): array
    {
        return [];
    }

    public static function getFilters(): array
    {
        return [];
    }

    public static function getRelationships(): array
    {
        return [];
    }

    public static function getSorts(): array
    {
        return [];
    }

    #[Override()]
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'id' => $this->getId(),
            'attributes' => $this->getAttributes(),
        ];
    }
}
