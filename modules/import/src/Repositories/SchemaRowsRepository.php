<?php

declare(strict_types=1);

namespace Modules\Import\Repositories;

use Modules\Import\DTO\SchemaRowDto;
use Yii;
use yii\mongodb\Collection;

/**
 * Denormalized "schema rows" imported from XLSX.
 * Kept for ES transfer and aggregation pipelines.
 */
final class SchemaRowsRepository extends BaseRepository
{
    public function collectionName(): string
    {
        return 'schema_rows';
    }

    public function getCollection(): Collection
    {
        return Yii::$app->mongodb->getCollection($this->collectionName());
    }

    public function buildUpsertOp(SchemaRowDto $row): array
    {
        $doc = $row->toDocument();

        return [
            'type' => 'update',
            'condition' => ['_id' => $row->id],
            'document' => ['$set' => $doc],
            'options' => ['upsert' => true],
        ];
    }
}

