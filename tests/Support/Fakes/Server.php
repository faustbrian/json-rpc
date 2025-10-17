<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Support\Fakes;

use Cline\JsonRpc\Servers\AbstractServer;
use Override;
use Tests\Support\Fakes\Methods\GetData;
use Tests\Support\Fakes\Methods\NotifyHello;
use Tests\Support\Fakes\Methods\NotifySum;
use Tests\Support\Fakes\Methods\Subtract;
use Tests\Support\Fakes\Methods\SubtractWithBinding;
use Tests\Support\Fakes\Methods\Sum;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class Server extends AbstractServer
{
    #[Override()]
    public function methods(): array
    {
        return [
            GetData::class,
            NotifyHello::class,
            NotifySum::class,
            Subtract::class,
            SubtractWithBinding::class,
            Sum::class,
        ];
    }
}
