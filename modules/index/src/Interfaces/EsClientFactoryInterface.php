<?php

declare(strict_types=1);

namespace Modules\Index\Interfaces;

use Elasticsearch\Client;

interface EsClientFactoryInterface
{
    public function create(string $host): Client;

    public function ensureIndex(Client $client, string $index): bool;
}

