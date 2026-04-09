<?php

declare(strict_types=1);

return [
    'container' => [
        'definitions' => [
            \Modules\Import\Console\Controllers\ImportController::class => [
                'class' => \Modules\Import\Console\Controllers\ImportController::class,
                '__construct()' => [
                    3 => \yii\di\Instance::of(\Modules\Config\Services\EnvInterface::class),
                    4 => \yii\di\Instance::of(\Psr\Log\LoggerInterface::class),
                ],
            ],
        ],
    ],
];

