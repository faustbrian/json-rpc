<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\JsonRpc\Contracts\ResourceInterface;
use Cline\JsonRpc\Exceptions\InternalErrorException;
use Cline\JsonRpc\Repositories\ResourceRepository;
use Tests\Support\Models\Post;
use Tests\Support\Models\User;
use Tests\Support\Resources\PostResource;
use Tests\Support\Resources\UserResource;

it('registers and retrieves a resource', function (): void {
    ResourceRepository::register(User::class, UserResource::class);

    $resource = ResourceRepository::get(
        new User(),
    );

    expect($resource)->toBeInstanceOf(ResourceInterface::class);
    expect($resource)->toBeInstanceOf(UserResource::class);
});

it('throws an exception when a resource is not found for a model', function (): void {
    ResourceRepository::forget(User::class);

    ResourceRepository::get(
        new User(),
    );
})->throws(InternalErrorException::class);

it('retrieves all registered resources', function (): void {
    ResourceRepository::register(Post::class, PostResource::class);
    ResourceRepository::register(User::class, UserResource::class);

    $resources = ResourceRepository::all();

    expect($resources)->toHaveCount(2);
    expect($resources)->toHaveKey(Post::class);
    expect($resources)->toHaveKey(User::class);
});
