<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Repositories;

use Cline\JsonRpc\Contracts\MethodInterface;
use Cline\JsonRpc\Exceptions\MethodNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use RuntimeException;

use function is_string;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class MethodRepository
{
    /** @var array<string, MethodInterface> */
    private array $methods = [];

    public function __construct(array $methods = [])
    {
        foreach ($methods as $method) {
            $this->register($method);
        }
    }

    public function all(): array
    {
        return $this->methods;
    }

    public function get(string $method): MethodInterface
    {
        $method = $this->methods[$method] ?? null;

        if ($method === null) {
            throw MethodNotFoundException::create();
        }

        return $method;
    }

    public function register(string|MethodInterface $method): void
    {
        if (is_string($method)) {
            /** @var MethodInterface $method */
            $method = App::make($method);
        }

        $methodName = $method->getName();

        if (Arr::has($this->methods, $methodName)) {
            throw new RuntimeException('Method already registered: '.$methodName);
        }

        $this->methods[$methodName] = $method;
    }
}
