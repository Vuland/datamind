<?php

declare(strict_types=1);

namespace Modules\Index\Services;

use Modules\Import\Repositories\SchemaRowsRepository;
use Modules\Index\DTO\EsTransferResult;
use Elasticsearch\Client;
use Modules\Index\Interfaces\MongoToEsTransferServiceInterface;

final class MongoToEsTransferService implements MongoToEsTransferServiceInterface
{
    public function __construct(
        private readonly SchemaRowsRepository $rows,
    ) {
    }

    public function transfer(Client $client, string $index, int $batchSize = 50): EsTransferResult
    {
        $collection = $this->rows->getCollection();

        $body = [];
        $count = 0;
        foreach ($collection->find([], [], ['batchSize' => $batchSize]) as $doc) {
            $cols = $doc;
            unset($cols['_id']);

            $payload = [
                'area' => $doc['area'] ?? null,
                'product' => $doc['product'] ?? null,
                'quantity' => $doc['quantity'] ?? null,
                'cols' => $cols,
            ];

            $body[] = ['update' => ['_index' => $index, '_id' => $doc['_id']]];
            $body[] = ['doc' => $payload, 'doc_as_upsert' => true];
            $count++;

            if ($count % $batchSize === 0) {
                $client->bulk(['body' => $body, 'refresh' => false]);
                $body = [];
            }
        }

        if ($body) {
            $client->bulk(['body' => $body, 'refresh' => true]);
        }

        return new EsTransferResult($count);
    }
}
