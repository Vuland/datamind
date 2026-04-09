<?php

declare(strict_types=1);

namespace Modules\Import\Repositories;

use Modules\Import\DTO\WarehouseDto;

final class WarehousesRepository extends BaseRepository
{
    public function collectionName(): string
    {
        return 'warehouses';
    }

    /**
     * @param list<WarehouseDto> $dtos
     */
    public function upsertBatch(array $dtos, array $options = []): void
    {
        $docs = array_map(
            static fn(WarehouseDto $d): array => $d->toDocument(),
            $dtos,
        );
        $this->upsertByUidBatch($docs, $options);
    }
}

