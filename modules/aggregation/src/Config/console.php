<?php

declare(strict_types=1);

return [
    'container' => [
        'definitions' => [
            \Modules\Aggregation\Console\Controllers\ReportController::class => [
                'class' => \Modules\Aggregation\Console\Controllers\ReportController::class,
                '__construct()' => [
                    4 => \yii\di\Instance::of(\Psr\Log\LoggerInterface::class),
                ],
            ],
        ],
    ],
];

