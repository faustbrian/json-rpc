<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Servers;

use Cline\JsonRpc\Contracts\MethodInterface;
use Cline\JsonRpc\Data\Configuration\ServerData;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Override;

use function class_implements;
use function collect;
use function config;
use function in_array;
use function once;
use function str_replace;
use function ucfirst;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ConfigurationServer extends AbstractServer
{
    public function __construct(
        private readonly ServerData $server,
    ) {
        parent::__construct();
    }

    #[Override()]
    public function getName(): string
    {
        return $this->server->name;
    }

    #[Override()]
    public function getRoutePath(): string
    {
        return $this->server->path;
    }

    #[Override()]
    public function getRouteName(): string
    {
        return $this->server->route;
    }

    #[Override()]
    public function getVersion(): string
    {
        return $this->server->version;
    }

    #[Override()]
    public function getMiddleware(): array
    {
        return $this->server->middleware;
    }

    #[Override()]
    public function methods(): array
    {
        return once(function (): array {
            $methods = $this->server->methods;

            if ($methods === null) {
                $methodsPath = (string) config('rpc.paths.methods');

                if (!File::isDirectory($methodsPath)) {
                    return [];
                }

                $methodsNamespace = (string) config('rpc.namespaces.methods');

                return collect(File::allFiles($methodsPath))
                    ->map(fn ($file): string => $file->getPathname())
                    ->filter(fn ($file): bool => Str::endsWith($file, ['.php']))
                    ->map(fn ($file): string => str_replace($methodsPath, $methodsNamespace, $file))
                    ->map(fn ($file): string => str_replace('.php', '', $file))
                    ->map(fn ($file): string => str_replace('/', '\\', $file))
                    ->map(fn ($file): string => ucfirst($file))
                    ->reject(fn ($file): bool => Str::contains($file, ['Abstract', 'Test']))
                    ->filter(fn ($file): bool => in_array(MethodInterface::class, (array) class_implements($file), true))
                    ->toArray();
            }

            return $methods;
        });
    }

    #[Override()]
    public function getContentDescriptors(): array
    {
        return $this->server->content_descriptors;
    }

    #[Override()]
    public function getSchemas(): array
    {
        return $this->server->schemas;
    }
}
