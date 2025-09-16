<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Methods\Concerns;

use Cline\JsonRpc\Contracts\ResourceInterface;
use Cline\JsonRpc\Data\DocumentData;
use Cline\JsonRpc\Data\RequestObjectData;
use Cline\JsonRpc\QueryBuilders\QueryBuilder;
use Cline\JsonRpc\Transformers\Transformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @property RequestObjectData $requestObject
 */
trait InteractsWithTransformer
{
    protected function item(Model|ResourceInterface $item): DocumentData
    {
        return Transformer::create($this->requestObject)->item($item);
    }

    protected function collection(Collection $collection): DocumentData
    {
        return Transformer::create($this->requestObject)->collection($collection);
    }

    protected function cursorPaginate(Builder|QueryBuilder $query): DocumentData
    {
        return Transformer::create($this->requestObject)->cursorPaginate($query);
    }

    protected function paginate(Builder|QueryBuilder $query): DocumentData
    {
        return Transformer::create($this->requestObject)->paginate($query);
    }

    protected function simplePaginate(Builder|QueryBuilder $query): DocumentData
    {
        return Transformer::create($this->requestObject)->simplePaginate($query);
    }
}
