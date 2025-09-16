<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Servers;

use Cline\JsonRpc\Contracts\ServerInterface;
use Cline\JsonRpc\Http\Middleware\BootServer;
use Cline\JsonRpc\Http\Middleware\ForceJson;
use Cline\JsonRpc\Methods\DiscoverMethod;
use Cline\JsonRpc\Repositories\MethodRepository;
use Cline\OpenRpc\ContentDescriptor\CursorPaginatorContentDescriptor;
use Cline\OpenRpc\Schema\CursorPaginatorSchema;
use Illuminate\Support\Facades\Config;
use Override;

/**
 * @author Brian Faust <brian@cline.sh>
 */
abstract class AbstractServer implements ServerInterface
{
    private readonly MethodRepository $methodRepository;

    public function __construct()
    {
        $this->methodRepository = new MethodRepository($this->methods());
        $this->methodRepository->register(DiscoverMethod::class);
    }

    #[Override()]
    public function getName(): string
    {
        return (string) Config::get('app.name');
    }

    #[Override()]
    public function getRoutePath(): string
    {
        return '/rpc';
    }

    #[Override()]
    public function getRouteName(): string
    {
        return 'rpc';
    }

    #[Override()]
    public function getVersion(): string
    {
        return '1.0.0';
    }

    #[Override()]
    public function getMiddleware(): array
    {
        return [
            ForceJson::class,
            BootServer::class,
        ];
    }

    #[Override()]
    public function getMethodRepository(): MethodRepository
    {
        return $this->methodRepository;
    }

    #[Override()]
    public function getContentDescriptors(): array
    {
        return [
            CursorPaginatorContentDescriptor::create(),
        ];
    }

    #[Override()]
    public function getSchemas(): array
    {
        return [
            CursorPaginatorSchema::create(),
        ];
    }

    abstract public function methods(): array;
}
