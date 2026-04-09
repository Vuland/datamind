<?php

declare(strict_types=1);

namespace Modules\Index\DTO;

final class EsTransferRequest
{
    public function __construct(
        public readonly string $host,
        public readonly string $index,
        public readonly int $batchSize = 500,
    ) {
    }
}

