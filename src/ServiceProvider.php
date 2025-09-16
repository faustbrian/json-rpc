<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc;

use Cline\JsonRpc\Data\Configuration\ConfigurationData;
use Cline\JsonRpc\Mixins\RouteMixin;
use Cline\JsonRpc\Repositories\ResourceRepository;
use Cline\JsonRpc\Repositories\ServerRepository;
use Cline\JsonRpc\Requests\RequestHandler;
use Cline\JsonRpc\Servers\ConfigurationServer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Override;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Throwable;

use function config;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ServiceProvider extends PackageServiceProvider
{
    #[Override()]
    public function configurePackage(Package $package): void
    {
        $package
            ->name('json-rpc')
            ->hasConfigFile(['json-rpc', 'rpc'])
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->publishConfigFile();
                $command->publishMigrations();
            });
    }

    #[Override()]
    public function packageRegistered(): void
    {
        $this->app->singleton(ServerRepository::class);
        $this->app->singleton(RequestHandler::class);
    }

    #[Override()]
    public function bootingPackage(): void
    {
        Route::mixin(
            new RouteMixin(),
        );
    }

    #[Override()]
    public function packageBooted(): void
    {
        try {
            $configuration = ConfigurationData::validateAndCreate((array) config('rpc'));

            foreach ($configuration->resources as $model => $resource) {
                ResourceRepository::register($model, $resource);
            }

            foreach ($configuration->servers as $server) {
                // @phpstan-ignore-next-line
                Route::rpc(
                    new ConfigurationServer($server),
                );
            }
        } catch (Throwable $throwable) {
            if (App::runningInConsole()) {
                return;
            }

            throw $throwable;
        }
    }
}
