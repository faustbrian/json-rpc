<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\Clients;

use Cline\JsonRpc\Data\RequestObjectData;
use Cline\JsonRpc\Data\ResponseData;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;

use function count;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class Client
{
    private array $batch = [];

    private readonly PendingRequest $client;

    public function __construct(string $host)
    {
        $this->client = Http::baseUrl($host)->asJson();
    }

    public static function create(string $host): self
    {
        return new self($host);
    }

    public function add(RequestObjectData $request): self
    {
        $this->batch = $request->jsonSerialize();

        return $this;
    }

    /**
     * @param list<RequestObjectData> $requests
     */
    public function addMany(array $requests): self
    {
        foreach ($requests as $request) {
            $this->add($request);
        }

        return $this;
    }

    /**
     * @return DataCollection<ResponseData>|ResponseData
     */
    public function request(): DataCollection|ResponseData
    {
        $response = (array) $this->client->post(
            '/',
            $this->isBatch() ? $this->batch : $this->batch[0],
        )->json();

        if ($this->isBatch()) {
            // @phpstan-ignore-next-line
            return ResponseData::collect($response);
        }

        return ResponseData::from($response);
    }

    private function isBatch(): bool
    {
        return count($this->batch) > 1;
    }
}
