<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Data\Configuration;

use Cline\JsonRpc\Data\AbstractData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Present;
use Spatie\LaravelData\DataCollection;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ConfigurationData extends AbstractData
{
    public function __construct(
        public readonly array $namespaces,
        public readonly array $paths,
        #[Present()]
        public readonly array $resources,
        #[DataCollectionOf(ServerData::class)]
        public readonly DataCollection $servers,
    ) {}
}
