<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\JsonRpc\Rules\Identifier;
use Illuminate\Support\Facades\Validator;

it('validates correctly with an integer', function (): void {
    $validator = Validator::make(
        ['value' => 123],
        ['value' => [new Identifier()]],
    );

    expect($validator->passes())->toBeTrue();
});

it('validates correctly with a string', function (): void {
    $validator = Validator::make(
        ['value' => 'string'],
        ['value' => [new Identifier()]],
    );

    expect($validator->passes())->toBeTrue();
});

it('validates correctly with null', function (): void {
    $validator = Validator::make(
        ['value' => null],
        ['value' => [new Identifier()]],
    );

    expect($validator->passes())->toBeTrue();
});

it('fails validation for an array', function (): void {
    $validator = Validator::make(
        ['value' => []],
        ['value' => [new Identifier()]],
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('value'))->toBe('The value must be an integer, string or null.');
});

it('fails validation for an object', function (): void {
    $validator = Validator::make(
        ['value' => new stdClass()],
        ['value' => [new Identifier()]],
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('value'))->toBe('The value must be an integer, string or null.');
});

it('fails validation for a boolean', function (): void {
    $validator = Validator::make(
        ['value' => true],
        ['value' => [new Identifier()]],
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('value'))->toBe('The value must be an integer, string or null.');
});
