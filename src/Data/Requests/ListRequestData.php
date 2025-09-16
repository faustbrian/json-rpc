<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Data\Requests;

use Cline\JsonRpc\Data\AbstractData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ListRequestData extends AbstractData
{
    public function __construct(
        public readonly ?array $fields,
        #[DataCollectionOf(FilterData::class)]
        public readonly ?DataCollection $filters,
        public readonly ?array $relationships,
        #[DataCollectionOf(SortData::class)]
        public readonly ?DataCollection $sorts,
    ) {}
}
