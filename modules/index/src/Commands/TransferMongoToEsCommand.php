<?php

declare(strict_types=1);

namespace Modules\Index\Commands;

use Modules\Index\DTO\EsTransferRequest;
use Modules\Index\DTO\EsTransferResult;
use Modules\Index\Interfaces\EsClientFactoryInterface;
use Modules\Index\Interfaces\MongoToEsTransferServiceInterface;

final class TransferMongoToEsCommand
{
    public function __construct(
        private readonly EsClientFactoryInterface $clientFactory,
        private readonly MongoToEsTransferServiceInterface $transferService,
    ) {
    }

    public function execute(EsTransferRequest $request): EsTransferResult
    {
        $client = $this->clientFactory->create($request->host);

        return $this->transferService->transfer($client, $request->index, $request->batchSize);
    }
}

