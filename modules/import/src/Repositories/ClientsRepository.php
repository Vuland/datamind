<?php

declare(strict_types=1);

namespace Modules\Import\Repositories;

use Modules\Import\DTO\ClientDto;

final class ClientsRepository extends BaseRepository
{
    public function collectionName(): string
    {
        return 'clients';
    }

    /**
     * @param list<ClientDto> $dtos
     */
    public function upsertBatch(array $dtos, array $options = []): void
    {
        $docs = array_map(
            static fn(ClientDto $d): array => $d->toDocument(),
            $dtos,
        );
        $this->upsertByUidBatch($docs, $options);
    }
}

