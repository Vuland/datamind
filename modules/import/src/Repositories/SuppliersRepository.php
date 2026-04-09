<?php

declare(strict_types=1);

namespace Modules\Import\Repositories;

use Modules\Import\DTO\SupplierDto;

final class SuppliersRepository extends BaseRepository
{
    public function collectionName(): string
    {
        return 'suppliers';
    }

    /**
     * @param list<SupplierDto> $dtos
     */
    public function upsertBatch(array $dtos, array $options = []): void
    {
        $docs = array_map(
            static fn(SupplierDto $d): array => $d->toDocument(),
            $dtos,
        );
        $this->upsertByUidBatch($docs, $options);
    }
}

