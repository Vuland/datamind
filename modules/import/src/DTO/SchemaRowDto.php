<?php

declare(strict_types=1);

namespace Modules\Import\DTO;

use Webmozart\Assert\Assert;

final class SchemaRowDto
{
    public function __construct(
        public readonly string $id,
        public readonly array $fields,
        public readonly array $refs,
    ) {
        Assert::stringNotEmpty($this->id, 'schema_row.id must not be empty');
    }

    public function toDocument(): array
    {
        return ['_id' => $this->id] + $this->fields + $this->refs;
    }
}

