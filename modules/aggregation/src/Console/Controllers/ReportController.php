<?php

declare(strict_types=1);

namespace Modules\Aggregation\Console\Controllers;

use Modules\Aggregation\Commands\GenerateReportCommand;
use Modules\Config\Enums\EnvKey;
use Modules\Config\Services\EnvInterface;
use Psr\Log\LoggerInterface;
use yii\console\Controller;
use yii\console\ExitCode;

final class ReportController extends Controller
{
    public string $esHost = '';
    public string $esIndex = '';

    public function __construct(
        $id,
        $module,
        private readonly GenerateReportCommand $command,
        private readonly EnvInterface $env,
        private readonly LoggerInterface $logger,
        array $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), ['esHost', 'esIndex']);
    }

    public function actionIndex(): int
    {
        $host = $this->esHost ?: $this->env->get(EnvKey::EsHost);
        $index = $this->esIndex ?: $this->env->get(EnvKey::EsIndex);

        $this->logger->info('Aggregation report (Elasticsearch): start', ['index' => $index]);

        foreach ($this->command->execute($host, $index) as $row) {
            $area = $row['_id']['area'] ?? '';
            $product = $row['_id']['product'] ?? '';
            $qty = $row['totalQuantity'] ?? 0;

            $this->logger->info('Aggregation row', [
                'area' => $area,
                'product' => $product,
                'totalQuantity' => $qty,
            ]);
        }

        return ExitCode::OK;
    }
}

