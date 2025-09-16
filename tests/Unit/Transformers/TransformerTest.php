<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\CarbonImmutable;
use Cline\JsonRpc\Data\DocumentData;
use Cline\JsonRpc\Data\RequestObjectData;
use Cline\JsonRpc\Repositories\ResourceRepository;
use Cline\JsonRpc\Transformers\Transformer;
use Illuminate\Support\Collection;
use Tests\Support\Models\Post;
use Tests\Support\Models\User;
use Tests\Support\Resources\PostResource;
use Tests\Support\Resources\UserResource;

beforeEach(function (): void {
    ResourceRepository::register(Post::class, PostResource::class);
    ResourceRepository::register(User::class, UserResource::class);
});

it('transforms a model to a document data structure', function (): void {
    $document = Transformer::create(
        RequestObjectData::from([
            'jsonrpc' => '2.0',
            'id' => '1',
            'method' => 'get',
        ]),
    )->item(
        User::create([
            'name' => 'John',
            'created_at' => CarbonImmutable::parse('01.01.2024'),
            'updated_at' => CarbonImmutable::parse('01.01.2024'),
        ]),
    );

    expect($document)->toBeInstanceOf(DocumentData::class);
    expect($document)->toMatchSnapshot();
});

it('transforms a collection to a document data structure', function (): void {
    $document = Transformer::create(
        RequestObjectData::from([
            'jsonrpc' => '2.0',
            'id' => '1',
            'method' => 'get',
        ]),
    )->collection(
        new Collection([
            User::create([
                'name' => 'John',
                'created_at' => CarbonImmutable::parse('01.01.2024'),
                'updated_at' => CarbonImmutable::parse('01.01.2024'),
            ]),
            User::create([
                'name' => 'Jane',
                'created_at' => CarbonImmutable::parse('01.01.2024'),
                'updated_at' => CarbonImmutable::parse('01.01.2024'),
            ]),
        ]),
    );

    expect($document)->toBeInstanceOf(DocumentData::class);
    expect($document)->toMatchSnapshot();
});

it('transforms cursor paginated results to a document data structure', function (): void {
    foreach (range(1, 101) as $index) {
        User::create([
            'name' => 'John '.$index,
            'created_at' => CarbonImmutable::parse('01.01.2024'),
            'updated_at' => CarbonImmutable::parse('01.01.2024'),
        ]);
    }

    $document = Transformer::create(
        RequestObjectData::from([
            'jsonrpc' => '2.0',
            'id' => '1',
            'method' => 'get',
        ]),
    )->cursorPaginate(
        UserResource::query(
            RequestObjectData::from([
                'jsonrpc' => '2.0',
                'id' => '1',
                'method' => 'get',
            ]),
        ),
    );

    expect($document)->toBeInstanceOf(DocumentData::class);
    expect($document)->toMatchSnapshot();
});

it('transforms length-aware paginated results to a document data structure', function (): void {
    foreach (range(1, 101) as $index) {
        User::create([
            'name' => 'John '.$index,
            'created_at' => CarbonImmutable::parse('01.01.2024'),
            'updated_at' => CarbonImmutable::parse('01.01.2024'),
        ]);
    }

    $requestObject = RequestObjectData::from([
        'jsonrpc' => '2.0',
        'id' => '1',
        'method' => 'get',
    ]);

    $document = Transformer::create($requestObject)->paginate(UserResource::query($requestObject));

    expect($document)->toBeInstanceOf(DocumentData::class);
    expect($document)->toMatchSnapshot();
});

it('transforms simply paginated results to a document data structure', function (): void {
    foreach (range(1, 101) as $index) {
        User::create([
            'name' => 'John '.$index,
            'created_at' => CarbonImmutable::parse('01.01.2024'),
            'updated_at' => CarbonImmutable::parse('01.01.2024'),
        ]);
    }

    $requestObject = RequestObjectData::from([
        'jsonrpc' => '2.0',
        'id' => '1',
        'method' => 'get',
    ]);

    $document = Transformer::create($requestObject)->simplePaginate(UserResource::query($requestObject));

    expect($document)->toBeInstanceOf(DocumentData::class);
    expect($document)->toMatchSnapshot();
});
