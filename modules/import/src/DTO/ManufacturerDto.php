<?php

declare(strict_types=1);

namespace Modules\Import\DTO;

use Webmozart\Assert\Assert;

final class ManufacturerDto
{
    public function __construct(
        public readonly string $uid,
        public readonly string $name,
    ) {
        Assert::stringNotEmpty($this->uid, 'manufacturer.uid must not be empty');
        Assert::stringNotEmpty($this->name, 'manufacturer.name must not be empty');
    }

    public function toDocument(): array
    {
        return [
            'uid' => $this->uid,
            'name' => $this->name,
        ];
    }
}

