<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\CarbonImmutable;
use Cline\JsonRpc\Data\ResourceObjectData;
use Cline\JsonRpc\Normalizers\ModelNormalizer;
use Cline\JsonRpc\Repositories\ResourceRepository;
use Tests\Support\Models\Post;
use Tests\Support\Models\User;
use Tests\Support\Resources\PostResource;
use Tests\Support\Resources\UserResource;

beforeEach(function (): void {
    ResourceRepository::register(Post::class, PostResource::class);
    ResourceRepository::register(User::class, UserResource::class);
});

it('transforms a model to a document data structure', function (): void {
    $user = User::create([
        'name' => 'John',
        'created_at' => CarbonImmutable::parse('01.01.2024'),
        'updated_at' => CarbonImmutable::parse('01.01.2024'),
    ]);

    Post::create([
        'user_id' => $user->id,
        'name' => 'John',
        'created_at' => CarbonImmutable::parse('01.01.2024'),
        'updated_at' => CarbonImmutable::parse('01.01.2024'),
    ]);

    $document = ModelNormalizer::normalize($user->load('posts'));

    expect($document)->toBeInstanceOf(ResourceObjectData::class);
    expect($document)->toMatchSnapshot();
});
