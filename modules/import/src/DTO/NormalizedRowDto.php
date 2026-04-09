<?php

declare(strict_types=1);

namespace Modules\Import\DTO;

final class NormalizedRowDto
{
    public function __construct(
        public readonly ManufacturerDto $manufacturer,
        public readonly SupplierDto $supplier,
        public readonly WarehouseDto $warehouse,
        public readonly ProductDto $product,
        public readonly ClientDto $client,
    ) {
    }

    public function rowRefs(): array
    {
        return [
            'manufacturer_uid' => $this->manufacturer->uid,
            'supplier_uid' => $this->supplier->uid,
            'warehouse_uid' => $this->warehouse->uid,
            'product_uid' => $this->product->uid,
            'client_uid' => $this->client->uid,
        ];
    }
}

