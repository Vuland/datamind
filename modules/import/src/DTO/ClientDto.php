<?php

declare(strict_types=1);

namespace Modules\Import\DTO;

use Webmozart\Assert\Assert;

final class ClientDto
{
    public function __construct(
        public readonly string $uid,
        public readonly string $code,
        public readonly string $divisionCode,
        public readonly string $okpo,
        public readonly string $name,
        public readonly string $legalAddress,
        public readonly string $deliveryAddress,
        public readonly string $area,
        public readonly string $city,
        public readonly string $firm,
    ) {
        Assert::stringNotEmpty($this->uid, 'client.uid must not be empty');
    }

    public function toDocument(): array
    {
        return [
            'uid' => $this->uid,
            'code' => $this->code,
            'division_code' => $this->divisionCode,
            'okpo' => $this->okpo,
            'name' => $this->name,
            'legal_address' => $this->legalAddress,
            'delivery_address' => $this->deliveryAddress,
            'area' => $this->area,
            'city' => $this->city,
            'firm' => $this->firm,
        ];
    }
}

