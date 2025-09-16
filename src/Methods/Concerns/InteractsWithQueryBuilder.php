<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Methods\Concerns;

use Cline\JsonRpc\Data\RequestObjectData;
use Cline\JsonRpc\QueryBuilders\QueryBuilder;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @property RequestObjectData $requestObject
 */
trait InteractsWithQueryBuilder
{
    protected function query(string $class): QueryBuilder
    {
        return $class::query($this->requestObject);
    }
}
