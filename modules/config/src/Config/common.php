<?php

declare(strict_types=1);

return [
    'modules' => [
        \Modules\Config\Module::ID => [
            'class' => \Modules\Config\Module::class,
        ],
    ],
    'container' => [
        'singletons' => [
            \Modules\Config\Services\EnvInterface::class => \Modules\Config\Services\Env::class,
        ],
    ],
];

