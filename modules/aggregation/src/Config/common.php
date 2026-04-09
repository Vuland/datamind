<?php

declare(strict_types=1);

return [
    'modules' => [
        \Modules\Aggregation\Module::ID => [
            'class' => \Modules\Aggregation\Module::class,
        ],
    ],
    'container' => [
        'singletons' => [
            \Modules\Aggregation\Interfaces\AggregationReportServiceInterface::class => \Modules\Aggregation\Services\EsAggregationReportService::class,
            \Modules\Aggregation\Services\EsAggregationReportService::class => \Modules\Aggregation\Services\EsAggregationReportService::class,
            \Modules\Aggregation\Commands\GenerateReportCommand::class => \Modules\Aggregation\Commands\GenerateReportCommand::class,
        ],
    ],
];

