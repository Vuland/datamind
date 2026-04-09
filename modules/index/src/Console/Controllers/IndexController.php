<?php

declare(strict_types=1);

namespace Modules\Index\Console\Controllers;

use Modules\Config\Enums\EnvKey;
use Modules\Config\Services\EnvInterface;
use Modules\Index\Commands\EnsureIndexCommand;
use Modules\Index\Commands\TransferMongoToEsCommand;
use Modules\Index\DTO\EsInitRequest;
use Modules\Index\DTO\EsTransferRequest;
use Psr\Log\LoggerInterface;
use yii\console\Controller;
use yii\console\ExitCode;

final class IndexController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly EnsureIndexCommand $ensureIndex,
        private readonly TransferMongoToEsCommand $transfer,
        private readonly EnvInterface $env,
        private readonly LoggerInterface $logger,
        $config = [],
    )
    {
        parent::__construct($id, $module, $config);
    }

    public function actionTransfer(int $batchSize = 500): int
    {
        $host = $this->env->get(EnvKey::EsHost);
        $index = $this->env->get(EnvKey::EsIndex);

        $request = new EsTransferRequest($host, $index, $batchSize);

        $this->maybeSendInit($host, $index);
        $result = $this->transfer->execute($request);

        $this->logger->info('Done. Total upserted to ES', ['totalUpserted' => $result->totalUpserted]);
        $this->logger->info('Re-run safe: ES documents are upserted by the same _id as Mongo.');

        return ExitCode::OK;
    }

    private function maybeSendInit(string $host, string $index): void
    {
        $created = $this->ensureIndex->execute(new EsInitRequest($host, $index));

        if ($created) {
            $this->logger->info('Created index', ['index' => $index]);
        }
    }
}

