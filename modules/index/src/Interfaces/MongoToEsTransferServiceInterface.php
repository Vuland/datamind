<?php

declare(strict_types=1);

namespace Modules\Index\Interfaces;

use Elasticsearch\Client;
use Modules\Index\DTO\EsTransferResult;

interface MongoToEsTransferServiceInterface
{
    public function transfer(Client $client, string $index, int $batchSize = 50): EsTransferResult;
}

