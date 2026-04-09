<?php

declare(strict_types=1);

return [
    'modules' => [
        \Modules\Index\Module::ID => [
            'class' => \Modules\Index\Module::class,
        ],
    ],
    'container' => [
        'singletons' => [
            \Modules\Import\Repositories\SchemaRowsRepository::class => \Modules\Import\Repositories\SchemaRowsRepository::class,
            \Modules\Index\Interfaces\EsClientFactoryInterface::class => \Modules\Index\Components\EsClientFactory::class,
            \Modules\Index\Interfaces\MongoToEsTransferServiceInterface::class => \Modules\Index\Services\MongoToEsTransferService::class,
            \Modules\Index\Components\EsClientFactory::class => \Modules\Index\Components\EsClientFactory::class,
            \Modules\Index\Services\MongoToEsTransferService::class => \Modules\Index\Services\MongoToEsTransferService::class,
            \Modules\Index\Commands\EnsureIndexCommand::class => \Modules\Index\Commands\EnsureIndexCommand::class,
            \Modules\Index\Commands\TransferMongoToEsCommand::class => \Modules\Index\Commands\TransferMongoToEsCommand::class,
        ],
    ],
];

