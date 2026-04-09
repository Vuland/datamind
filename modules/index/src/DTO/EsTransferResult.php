<?php

declare(strict_types=1);

namespace Modules\Index\DTO;

final class EsTransferResult
{
    public function __construct(
        public readonly int $totalUpserted,
    ) {
    }
}

