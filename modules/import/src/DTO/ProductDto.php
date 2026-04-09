<?php

declare(strict_types=1);

namespace Modules\Import\DTO;

use Webmozart\Assert\Assert;

final class ProductDto
{
    public function __construct(
        public readonly string $uid,
        public readonly string $code,
        public readonly string $barcode,
        public readonly string $name,
        public readonly string $morionCode,
        public readonly string $unit,
        public readonly string $manufacturerUid,
    ) {
        Assert::stringNotEmpty($this->uid, 'product.uid must not be empty');
        Assert::stringNotEmpty($this->manufacturerUid, 'product.manufacturer_uid must not be empty');

        Assert::true(
            $this->code !== '' || $this->barcode !== '' || $this->name !== '' || $this->morionCode !== '',
            'product must have at least one identifying field',
        );
    }

    public function toDocument(): array
    {
        return [
            'uid' => $this->uid,
            'code' => $this->code,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'morion_code' => $this->morionCode,
            'unit' => $this->unit,
            'manufacturer_uid' => $this->manufacturerUid,
        ];
    }
}

