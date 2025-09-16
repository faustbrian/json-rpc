<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\JsonRpc\Servers\ConfigurationServer;

arch('globals')
    ->expect(['dd', 'dump'])
    ->not->toBeUsed();

// arch('Cline\JsonRpc\Clients')
//     ->expect('Cline\JsonRpc\Clients')
//     ->toUseStrictTypes()
//     ->toBeFinal();

// arch('Cline\JsonRpc\Contracts')
//     ->expect('Cline\JsonRpc\Contracts')
//     ->toUseStrictTypes()
//     ->toBeInterfaces();

// arch('Cline\JsonRpc\Data')
//     ->expect('Cline\JsonRpc\Data')
//     ->toUseStrictTypes()
//     ->toBeFinal()
//     ->ignoring([
//         Cline\JsonRpc\Data\AbstractContentDescriptorData::class,
//         Cline\JsonRpc\Data\AbstractData::class,
//     ])
//     ->toHaveSuffix('Data')
//     ->toExtend(Spatie\LaravelData\Data::class);

// arch('Cline\JsonRpc\Exceptions')
//     ->expect('Cline\JsonRpc\Exceptions')
//     ->toUseStrictTypes()
//     ->toBeFinal()
//     ->ignoring([
//         Cline\JsonRpc\Exceptions\AbstractRequestException::class,
//         Cline\JsonRpc\Exceptions\Concerns\RendersThrowable::class,
//     ]);

// arch('Cline\JsonRpc\Facades')
//     ->expect('Cline\JsonRpc\Facades')
//     ->toUseStrictTypes()
//     ->toBeFinal();

// arch('Cline\JsonRpc\Http')
//     ->expect('Cline\JsonRpc\Http')
//     ->toUseStrictTypes()
//     ->toBeFinal();

// arch('Cline\JsonRpc\Jobs')
//     ->expect('Cline\JsonRpc\Jobs')
//     ->toUseStrictTypes()
//     ->toBeFinal()
//     ->toBeReadonly();

// arch('Cline\JsonRpc\Methods')
//     ->expect('Cline\JsonRpc\Methods')
//     ->toUseStrictTypes();

// arch('Cline\JsonRpc\Mixins')
//     ->expect('Cline\JsonRpc\Mixins')
//     ->toUseStrictTypes()
//     ->toBeFinal()
//     ->toBeReadonly();

// arch('Cline\JsonRpc\Normalizers')
//     ->expect('Cline\JsonRpc\Normalizers')
//     ->toUseStrictTypes()
//     ->toBeFinal()
//     ->toBeReadonly()
//     ->toHaveSuffix('Normalizer');

// arch('Cline\JsonRpc\QueryBuilders')
//     ->expect('Cline\JsonRpc\QueryBuilders')
//     ->toUseStrictTypes()
//     ->toBeFinal()
//     ->ignoring('Cline\JsonRpc\QueryBuilders\Concerns');

// arch('Cline\JsonRpc\Repositories')
//     ->expect('Cline\JsonRpc\Repositories')
//     ->toUseStrictTypes()
//     ->toBeFinal();

// arch('Cline\JsonRpc\Requests')
//     ->expect('Cline\JsonRpc\Requests')
//     ->toUseStrictTypes()
//     ->toBeFinal();

// arch('Cline\JsonRpc\Rules')
//     ->expect('Cline\JsonRpc\Rules')
//     ->toUseStrictTypes()
//     ->toBeFinal();

// arch('Cline\JsonRpc\Servers')
//     ->expect('Cline\JsonRpc\Servers')
//     ->toUseStrictTypes()
//     ->toBeAbstract()
//     ->ignoring(ConfigurationServer::class);

// arch('Cline\JsonRpc\Transformers')
//     ->expect('Cline\JsonRpc\Transformers')
//     ->toUseStrictTypes()
//     ->toBeFinal()
//     ->toHaveSuffix('Transformer');
