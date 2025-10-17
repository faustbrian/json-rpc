<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\JsonRpc\Repositories\ResourceRepository;
use Tests\Support\Models\User;
use Tests\Support\Resources\UserResource;

describe('AbstractModelResource', function (): void {
    beforeEach(function (): void {
        ResourceRepository::register(User::class, UserResource::class);
    });

    test('creates resource from model', function (): void {
        $user = User::query()->create([
            'name' => 'John Doe',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $resource = new UserResource($user);

        expect($resource->getId())->toBe((string) $user->id);
        expect($resource->getType())->toBe('user');
        expect($resource->getAttributes())->toHaveKey('name');
    });

    test('gets model class', function (): void {
        expect(UserResource::getModel())->toBe(User::class);
    });

    test('gets fields configuration', function (): void {
        $fields = UserResource::getFields();
        expect($fields)->toHaveKey('self');
        expect($fields['self'])->toContain('id', 'name');
    });

    test('gets filters configuration', function (): void {
        $filters = UserResource::getFilters();
        expect($filters)->toHaveKey('self');
    });

    test('gets relationships configuration', function (): void {
        $relationships = UserResource::getRelationships();
        expect($relationships)->toHaveKey('self');
    });

    test('gets sorts configuration', function (): void {
        $sorts = UserResource::getSorts();
        expect($sorts)->toHaveKey('self');
    });
});
