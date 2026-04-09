<?php

declare(strict_types=1);

namespace Modules\Import\Components;

use Modules\Import\DTO\ClientDto;
use Modules\Import\DTO\ManufacturerDto;
use Modules\Import\DTO\NormalizedRowDto;
use Modules\Import\DTO\ProductDto;
use Modules\Import\DTO\SupplierDto;
use Modules\Import\DTO\WarehouseDto;
use Ramsey\Uuid\Uuid;

final class ImportRowNormalizer
{
    private array $manufacturerUidByKey = [];

    private array $supplierUidByKey = [];

    private array $warehouseUidByKey = [];

    private array $productUidByKey = [];

    private array $clientUidByKey = [];

    public function normalize(array $fields): NormalizedRowDto
    {
        $manufacturerName = trim((string)($fields['manufacturer'] ?? ''));
        $supplierName = trim((string)($fields['supplier'] ?? ''));
        $warehouseName = trim((string)($fields['warehouse'] ?? ''));

        $productCode = trim((string)($fields['product_code'] ?? ''));
        $productBarcode = trim((string)($fields['product_barcode'] ?? ''));
        $productName = trim((string)($fields['product'] ?? ''));
        $productMorion = trim((string)($fields['morion_code'] ?? ''));
        $productUnit = trim((string)($fields['unit'] ?? ''));

        $clientCode = trim((string)($fields['client_code'] ?? ''));
        $clientSub = trim((string)($fields['client_division_code'] ?? ''));
        $clientOkpo = trim((string)($fields['client_okpo'] ?? ''));
        $clientName = trim((string)($fields['client'] ?? ''));
        $clientLegalAddress = trim((string)($fields['legal_address'] ?? ''));
        $clientDeliveryAddress = trim((string)($fields['delivery_address'] ?? ''));
        $clientArea = trim((string)($fields['area'] ?? ''));
        $clientCity = trim((string)($fields['city'] ?? ''));
        $firm = trim((string)($fields['firm'] ?? ''));

        $manufacturerKey = 'manufacturer:' . $manufacturerName;
        $supplierKey = 'supplier:' . $supplierName;
        $warehouseKey = 'warehouse:' . $warehouseName;
        $productKey = 'product:' . implode('|', [$productCode, $productBarcode, $productMorion, $productName]);
        $clientKey = 'client:' . implode('|', [$clientCode, $clientSub, $clientOkpo, $clientName]);

        $manufacturerUid = $this->manufacturerUidByKey[$manufacturerKey] ??= Uuid::uuid7()->toString();
        $supplierUid = $this->supplierUidByKey[$supplierKey] ??= Uuid::uuid7()->toString();
        $warehouseUid = $this->warehouseUidByKey[$warehouseKey] ??= Uuid::uuid7()->toString();
        $productUid = $this->productUidByKey[$productKey] ??= Uuid::uuid7()->toString();
        $clientUid = $this->clientUidByKey[$clientKey] ??= Uuid::uuid7()->toString();

        return new NormalizedRowDto(
            manufacturer: new ManufacturerDto($manufacturerUid, $manufacturerName),
            supplier: new SupplierDto($supplierUid, $supplierName),
            warehouse: new WarehouseDto($warehouseUid, $warehouseName),
            product: new ProductDto(
                uid: $productUid,
                code: $productCode,
                barcode: $productBarcode,
                name: $productName,
                morionCode: $productMorion,
                unit: $productUnit,
                manufacturerUid: $manufacturerUid,
            ),
            client: new ClientDto(
                uid: $clientUid,
                code: $clientCode,
                divisionCode: $clientSub,
                okpo: $clientOkpo,
                name: $clientName,
                legalAddress: $clientLegalAddress,
                deliveryAddress: $clientDeliveryAddress,
                area: $clientArea,
                city: $clientCity,
                firm: $firm,
            ),
        );
    }
}

