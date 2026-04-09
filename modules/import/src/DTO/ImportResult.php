<?php

declare(strict_types=1);

namespace Modules\Import\DTO;

final class ImportResult
{
    public function __construct(
        public readonly int $processedRows,
    ) {
    }
}
