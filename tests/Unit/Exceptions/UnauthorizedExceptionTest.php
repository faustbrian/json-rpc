<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\JsonRpc\Exceptions\AbstractRequestException;
use Cline\JsonRpc\Exceptions\UnauthorizedException;

it('creates an unauthorized exception', function (): void {
    $requestException = UnauthorizedException::create();

    expect($requestException)->toBeInstanceOf(AbstractRequestException::class);
    expect($requestException->toArray())->toMatchSnapshot();
    expect($requestException->getErrorCode())->toBe(-32_000);
    expect($requestException->getErrorMessage())->toBe('Server error');
});
