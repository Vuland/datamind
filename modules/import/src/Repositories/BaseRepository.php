<?php

declare(strict_types=1);

namespace Modules\Import\Repositories;

use Yii;

abstract class BaseRepository
{
    abstract public function collectionName(): string;

    public function executeBatch(array $ops, array $options = []): void
    {
        Yii::$app->mongodb->createCommand($ops)->executeBatch($this->collectionName(), $options);
    }

    public function upsertByUidBatch(array $docs, array $options = []): void
    {
        if ($docs === []) {
            return;
        }

        $ops = [];
        foreach ($docs as $doc) {
            $uid = (string)($doc['uid'] ?? '');
            if ($uid === '') {
                continue;
            }
            $ops[] = [
                'type' => 'update',
                'condition' => ['uid' => $uid],
                'document' => ['$set' => $doc],
                'options' => ['upsert' => true],
            ];
        }

        if ($ops !== []) {
            $this->executeBatch($ops, $options);
        }
    }
}

