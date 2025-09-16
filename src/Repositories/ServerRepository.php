<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Repositories;

use Cline\JsonRpc\Contracts\ServerInterface;
use Cline\JsonRpc\Exceptions\ServerNotFoundException;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class ServerRepository
{
    private Collection $servers;

    public function __construct()
    {
        $this->servers = new Collection();
    }

    /**
     * @return Collection<int, ServerInterface>
     */
    public function all(): Collection
    {
        return $this->servers;
    }

    public function findByName(string $name): ServerInterface
    {
        return $this->findBy(fn (ServerInterface $server): bool => $server->getRouteName() === $name);
    }

    public function findByPath(string $path): ServerInterface
    {
        return $this->findBy(fn (ServerInterface $server): bool => $server->getRoutePath() === $path);
    }

    public function register(string|ServerInterface $server): void
    {
        if (is_string($server)) {
            /** @var ServerInterface $server */
            $server = App::make($server);
        }

        $this->servers[$server->getRoutePath()] = $server;
    }

    private function findBy(Closure $closure): ServerInterface
    {
        $server = $this->servers->firstWhere(
            $closure,
            fn () => throw ServerNotFoundException::create(),
        );

        if ($server instanceof ServerInterface) {
            return $server;
        }

        throw ServerNotFoundException::create();
    }
}
