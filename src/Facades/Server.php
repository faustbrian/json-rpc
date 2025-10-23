<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\RPC\Facades;

use Cline\OpenRpc\ValueObject\ContentDescriptorValue;
use Cline\OpenRpc\ValueObject\SchemaValue;
use Cline\RPC\Contracts\ServerInterface;
use Cline\RPC\Repositories\MethodRepository;
use Illuminate\Support\Facades\Facade;
use Override;

/**
 * Facade for accessing the JSON-RPC server instance.
 *
 * This facade provides static access to the current JSON-RPC server's configuration,
 * metadata, and method repository. It proxies calls to the active ServerInterface
 * implementation bound in the service container. Use this facade to access server
 * properties like OpenRPC schemas, middleware, and routing information within your
 * JSON-RPC method implementations.
 *
 * @method static array<ContentDescriptorValue> getContentDescriptors() Retrieves the OpenRPC content descriptors that define the structure and types of method parameters and return values
 * @method static MethodRepository              getMethodRepository()   Gets the method repository containing all registered JSON-RPC methods for this server
 * @method static array<int, string>            getMiddleware()         Returns the middleware stack that will be applied to all requests handled by this server
 * @method static string                        getName()               Gets the human-readable name of this JSON-RPC server instance
 * @method static string                        getRouteName()          Returns the Laravel route name for this JSON-RPC server endpoint
 * @method static string                        getRoutePath()          Gets the URL path where this JSON-RPC server is mounted
 * @method static array<SchemaValue>            getSchemas()            Retrieves the OpenRPC schema definitions for request/response validation
 * @method static string                        getVersion()            Gets the version identifier of this JSON-RPC server implementation
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see ServerInterface
 */
final class Server extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string The service container binding key for the server interface
     */
    #[Override()]
    protected static function getFacadeAccessor(): string
    {
        return ServerInterface::class;
    }
}
