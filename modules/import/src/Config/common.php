<?php

declare(strict_types=1);

return [
    'modules' => [
        \Modules\Import\Module::ID => [
            'class' => \Modules\Import\Module::class,
        ],
    ],
    'container' => [
        'singletons' => [
            \Modules\Import\Repositories\SchemaRowsRepository::class => \Modules\Import\Repositories\SchemaRowsRepository::class,
            \Modules\Import\Repositories\ManufacturersRepository::class => \Modules\Import\Repositories\ManufacturersRepository::class,
            \Modules\Import\Repositories\SuppliersRepository::class => \Modules\Import\Repositories\SuppliersRepository::class,
            \Modules\Import\Repositories\WarehousesRepository::class => \Modules\Import\Repositories\WarehousesRepository::class,
            \Modules\Import\Repositories\ProductsRepository::class => \Modules\Import\Repositories\ProductsRepository::class,
            \Modules\Import\Repositories\ClientsRepository::class => \Modules\Import\Repositories\ClientsRepository::class,
            \Modules\Import\Components\MojibakeDecoder::class => \Modules\Import\Components\MojibakeDecoder::class,
            \Modules\Import\Components\ImportRowNormalizer::class => \Modules\Import\Components\ImportRowNormalizer::class,
            \Modules\Import\Validators\ImportSheetValidator::class => \Modules\Import\Validators\ImportSheetValidator::class,
            \Modules\Import\Services\XlsxMongoImportService::class => \Modules\Import\Services\XlsxMongoImportService::class,
            \Modules\Import\Commands\ImportXlsxToMongoCommand::class => \Modules\Import\Commands\ImportXlsxToMongoCommand::class,
        ],
    ],
];

