<?php

declare(strict_types=1);

namespace Modules\Import\DTO;

final class ImportRequest
{
    public function __construct(
        public readonly string $xlsxPath,
        public readonly int $batchSize = 50,
    ) {
    }
}

