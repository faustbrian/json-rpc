<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Facades;

use Cline\JsonRpc\Contracts\ServerInterface;
use Cline\JsonRpc\Repositories\MethodRepository;
use Cline\OpenRpc\ValueObject\ContentDescriptorValue;
use Cline\OpenRpc\ValueObject\SchemaValue;
use Illuminate\Support\Facades\Facade;
use Override;

/**
 * @method static array<ContentDescriptorValue> getContentDescriptors()
 * @method static MethodRepository              getMethodRepository()
 * @method static array                         getMiddleware()
 * @method static string                        getName()
 * @method static string                        getRouteName()
 * @method static string                        getRoutePath()
 * @method static array<SchemaValue>            getSchemas()
 * @method static string                        getVersion()
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class Server extends Facade
{
    #[Override()]
    protected static function getFacadeAccessor(): string
    {
        return ServerInterface::class;
    }
}
