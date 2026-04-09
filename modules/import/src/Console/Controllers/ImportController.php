<?php

declare(strict_types=1);

namespace Modules\Import\Console\Controllers;

use Modules\Config\Enums\EnvKey;
use Modules\Config\Services\EnvInterface;
use Modules\Import\Commands\ImportXlsxToMongoCommand;
use Modules\Import\DTO\ImportRequest;
use Psr\Log\LoggerInterface;
use yii\console\Controller;
use yii\console\ExitCode;

final class ImportController extends Controller
{
    private const DEFAULT_XLSX_PATH = '@app/data/input.xlsx';

    public function __construct(
        $id,
        $module,
        private readonly ImportXlsxToMongoCommand $command,
        private readonly EnvInterface $env,
        private readonly LoggerInterface $logger,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(int $batchSize = 50): int
    {
        $xlsxPath = $this->env->get(EnvKey::XlsxPath, self::DEFAULT_XLSX_PATH);
        $result = $this->command->execute(new ImportRequest($xlsxPath, $batchSize));

        $this->logger->info('Processed rows into MongoDB (upsert)', ['rows' => $result->processedRows]);

        return ExitCode::OK;
    }
}

