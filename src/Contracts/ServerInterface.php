<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Contracts;

use Cline\JsonRpc\Repositories\MethodRepository;

/**
 * @author Brian Faust <brian@cline.sh>
 */
interface ServerInterface
{
    public function getName(): string;

    public function getRoutePath(): string;

    public function getRouteName(): string;

    public function getVersion(): string;

    public function getMiddleware(): array;

    public function getMethodRepository(): MethodRepository;

    public function getContentDescriptors(): array;

    public function getSchemas(): array;
}
