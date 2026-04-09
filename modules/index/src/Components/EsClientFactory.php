<?php

declare(strict_types=1);

namespace Modules\Index\Components;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Modules\Index\Interfaces\EsClientFactoryInterface;

final class EsClientFactory implements EsClientFactoryInterface
{
    public function create(string $host): Client
    {
        return ClientBuilder::create()->setHosts([$host])->build();
    }

    public function ensureIndex(Client $client, string $index): bool
    {
        if ($client->indices()->exists(['index' => $index])) {
            return false;
        }

        $client->indices()->create([
            'index' => $index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                ],
                'mappings' => [
                    'dynamic' => true,
                    'properties' => [
                        'area' => ['type' => 'keyword'],
                        'product' => ['type' => 'keyword'],
                        'quantity' => ['type' => 'double'],
                        'updatedAt' => ['type' => 'date'],
                    ],
                ],
            ],
        ]);

        return true;
    }
}

