<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Methods;

use Cline\JsonRpc\Contracts\MethodInterface;
use Cline\JsonRpc\Data\RequestObjectData;
use Cline\OpenRpc\ValueObject\ContentDescriptorValue;
use Illuminate\Support\Str;
use Override;

use function class_basename;

/**
 * @author Brian Faust <brian@cline.sh>
 */
abstract class AbstractMethod implements MethodInterface
{
    use Concerns\InteractsWithAuthentication;
    use Concerns\InteractsWithQueryBuilder;
    use Concerns\InteractsWithTransformer;

    protected RequestObjectData $requestObject;

    #[Override()]
    public function getName(): string
    {
        return 'app.'.Str::snake(class_basename(static::class));
    }

    #[Override()]
    public function getSummary(): string
    {
        return $this->getName();
    }

    #[Override()]
    public function getParams(): array
    {
        return [];
    }

    #[Override()]
    public function getResult(): ?ContentDescriptorValue
    {
        return null;
    }

    #[Override()]
    public function getErrors(): array
    {
        return [];
    }

    #[Override()]
    public function setRequest(RequestObjectData $requestObject): void
    {
        $this->requestObject = $requestObject;
    }
}
