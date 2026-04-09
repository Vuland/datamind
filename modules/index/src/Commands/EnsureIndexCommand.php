<?php

declare(strict_types=1);

namespace Modules\Index\Commands;

use Modules\Index\DTO\EsInitRequest;
use Modules\Index\Interfaces\EsClientFactoryInterface;

final class EnsureIndexCommand
{
    public function __construct(
        private readonly EsClientFactoryInterface $clientFactory,
    )
    {
    }

    public function execute(EsInitRequest $request): bool
    {
        $client = $this->clientFactory->create($request->host);

        return $this->clientFactory->ensureIndex($client, $request->index);
    }
}

