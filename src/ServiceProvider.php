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
 * Laravel service provider for the JSON-RPC package.
 *
 * Handles package registration, configuration publishing, route registration,
 * and resource discovery. Automatically configures RPC servers based on the
 * published configuration file.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class ServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package's publishable assets and install commands.
     *
     * Defines the package name, configuration files to publish, and the installation
     * command that publishes configuration and migration files to the Laravel application.
     *
     * @param Package $package Package configuration instance
     */
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

    /**
     * Register package services in the Laravel container.
     *
     * Binds the ServerRepository and RequestHandler as singletons to ensure
     * consistent server and request handling throughout the application lifecycle.
     * These services are shared across all RPC requests.
     */
    #[Override()]
    public function packageRegistered(): void
    {
        $this->app->singleton(ServerRepository::class);
        $this->app->singleton(RequestHandler::class);
    }

    /**
     * Perform operations during package booting phase.
     *
     * Registers the custom Route mixin that adds the rpc() method to Laravel's
     * route facade, enabling convenient RPC server registration in route files.
     */
    #[Override()]
    public function bootingPackage(): void
    {
        Route::mixin(
            new RouteMixin(),
        );
    }

    /**
     * Boot package services after all providers are registered.
     *
     * Loads the RPC configuration, registers resource mappings, and creates
     * RPC server routes based on the configuration. Gracefully handles missing
     * or invalid configuration in console environments to prevent installation
     * errors before configuration is published.
     *
     * @throws Throwable Configuration validation errors in non-console environments
     */
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
