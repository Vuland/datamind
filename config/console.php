<?php

$params = require __DIR__ . '/params.php';
$moduleConfig = require __DIR__ . '/modules.php';
$env = new \Modules\Config\Services\Env();

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        \Modules\Import\Module::ID => [
            'class' => \Modules\Import\Module::class,
        ],
        \Modules\Index\Module::ID => [
            'class' => \Modules\Index\Module::class,
        ],
        'mongo-import' => [
            'class' => \Modules\Import\Module::class,
        ],
        'es-index' => [
            'class' => \Modules\Index\Module::class,
        ],
        \Modules\Aggregation\Module::ID => [
            'class' => \Modules\Aggregation\Module::class,
        ],
    ],
    'aliases' => [
        '@npm' => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'container' => [
        'singletons' => [
            \Psr\Log\LoggerInterface::class => static function () {
                $logger = new \Monolog\Logger('datamind-console');
                $handler = new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Level::Info);
                $handler->setFormatter(new \Monolog\Formatter\LineFormatter(null, null, true, true));
                $logger->pushHandler($handler);
                return $logger;
            },

            \Modules\Import\Services\XlsxMongoImportService::class => \Modules\Import\Services\XlsxMongoImportService::class,
            \Modules\Import\Commands\ImportXlsxToMongoCommand::class => \Modules\Import\Commands\ImportXlsxToMongoCommand::class,

            \Modules\Index\Components\EsClientFactory::class => \Modules\Index\Components\EsClientFactory::class,
            \Modules\Index\Services\MongoToEsTransferService::class => \Modules\Index\Services\MongoToEsTransferService::class,
            \Modules\Index\Commands\EnsureIndexCommand::class => \Modules\Index\Commands\EnsureIndexCommand::class,
            \Modules\Index\Commands\TransferMongoToEsCommand::class => \Modules\Index\Commands\TransferMongoToEsCommand::class,

            \Modules\Index\Interfaces\EsClientFactoryInterface::class => \Modules\Index\Components\EsClientFactory::class,
            \Modules\Index\Interfaces\MongoToEsTransferServiceInterface::class => \Modules\Index\Services\MongoToEsTransferService::class,

            \Modules\Aggregation\Services\EsAggregationReportService::class => \Modules\Aggregation\Services\EsAggregationReportService::class,
            \Modules\Aggregation\Interfaces\AggregationReportServiceInterface::class => \Modules\Aggregation\Services\EsAggregationReportService::class,
            \Modules\Aggregation\Commands\GenerateReportCommand::class => \Modules\Aggregation\Commands\GenerateReportCommand::class,
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mongodb' => [
            'class' => \yii\mongodb\Connection::class,
            'dsn' => $env->get(\Modules\Config\Enums\EnvKey::MongoDsn),
            'defaultDatabaseName' => $env->get(\Modules\Config\Enums\EnvKey::MongoDb),
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    // `yii2-debug` keeps a lot of logs in memory; for long-running console commands (imports)
    // it can easily exhaust memory. Enable it explicitly if you really need it.
    if (getenv('YII_CONSOLE_DEBUG') === '1') {
        $config['bootstrap'][] = 'debug';
        $config['modules']['debug'] = [
            'class' => 'yii\debug\Module',
        ];
    }
}

return \yii\helpers\ArrayHelper::merge($config, $moduleConfig);
