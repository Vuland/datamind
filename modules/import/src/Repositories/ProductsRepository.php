<?php

declare(strict_types=1);

namespace Modules\Import\Repositories;

use Modules\Import\DTO\ProductDto;

final class ProductsRepository extends BaseRepository
{
    public function collectionName(): string
    {
        return 'products';
    }

    /**
     * @param list<ProductDto> $dtos
     */
    public function upsertBatch(array $dtos, array $options = []): void
    {
        $docs = array_map(
            static fn(ProductDto $d): array => $d->toDocument(),
            $dtos,
        );
        $this->upsertByUidBatch($docs, $options);
    }
}

