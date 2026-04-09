<?php

declare(strict_types=1);

namespace Modules\Import\DTO;

use Webmozart\Assert\Assert;

final class SupplierDto
{
    public function __construct(
        public readonly string $uid,
        public readonly string $name,
    ) {
        Assert::stringNotEmpty($this->uid, 'supplier.uid must not be empty');
        Assert::stringNotEmpty($this->name, 'supplier.name must not be empty');
    }

    public function toDocument(): array
    {
        return [
            'uid' => $this->uid,
            'name' => $this->name,
        ];
    }
}

