<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\JsonRpc\Exceptions\AbstractRequestException;
use Cline\JsonRpc\Exceptions\InvalidDataException;
use Illuminate\Validation\ValidationException;

it('creates an invalid params exception from a validation exception', function (): void {
    $requestException = InvalidDataException::create(
        ValidationException::withMessages([
            'field' => ['The field is required.'],
        ]),
    );

    expect($requestException)->toBeInstanceOf(AbstractRequestException::class);
    expect($requestException->toArray())->toMatchSnapshot();
    expect($requestException->getErrorCode())->toBe(-32_602);
    expect($requestException->getErrorMessage())->toBe('Invalid params');
});
