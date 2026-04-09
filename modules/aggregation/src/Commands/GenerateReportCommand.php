<?php

declare(strict_types=1);

namespace Modules\Aggregation\Commands;

use Modules\Aggregation\Interfaces\AggregationReportServiceInterface;

final class GenerateReportCommand
{
    public function __construct(
        private readonly AggregationReportServiceInterface $service,
    ) {
    }

    public function execute(string $host, string $index): iterable
    {
        return $this->service->generate($host, $index);
    }
}

