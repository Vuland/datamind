<?php

declare(strict_types=1);

return [
    'container' => [
        'definitions' => [
            \Modules\Index\Console\Controllers\IndexController::class => [
                'class' => \Modules\Index\Console\Controllers\IndexController::class,
                '__construct()' => [
                    4 => \yii\di\Instance::of(\Modules\Config\Services\EnvInterface::class),
                    5 => \yii\di\Instance::of(\Psr\Log\LoggerInterface::class),
                ],
            ],
        ],
    ],
];

