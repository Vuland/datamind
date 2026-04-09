<?php

declare(strict_types=1);

namespace Modules\Aggregation\Interfaces;

interface AggregationReportServiceInterface
{
    public function generate(string $host, string $index, int $pageSize = 1000): iterable;
}

