<?php

declare(strict_types=1);

namespace Modules\Import\Repositories;

use Modules\Import\DTO\ManufacturerDto;

final class ManufacturersRepository extends BaseRepository
{
    public function collectionName(): string
    {
        return 'manufacturers';
    }

    /**
     * @param list<ManufacturerDto> $dtos
     */
    public function upsertBatch(array $dtos, array $options = []): void
    {
        $docs = array_map(
            static fn(ManufacturerDto $d): array => $d->toDocument(),
            $dtos,
        );
        $this->upsertByUidBatch($docs, $options);
    }
}

