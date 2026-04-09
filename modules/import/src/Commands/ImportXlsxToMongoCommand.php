<?php

declare(strict_types=1);

namespace Modules\Import\Commands;

use Modules\Import\DTO\ImportRequest;
use Modules\Import\DTO\ImportResult;
use Modules\Import\Services\XlsxMongoImportService;

final class ImportXlsxToMongoCommand
{
    public function __construct(
        private readonly XlsxMongoImportService $service,
    ) {
    }

    public function execute(ImportRequest $request): ImportResult
    {
        return $this->service->import($request);
    }
}

