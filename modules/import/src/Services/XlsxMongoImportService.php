<?php

declare(strict_types=1);

namespace Modules\Import\Services;

use Modules\Import\Components\ImportRowNormalizer;
use Modules\Import\Components\MojibakeDecoder;
use Modules\Import\Components\XlsxChunkReadFilter;
use Modules\Import\DTO\ImportRequest;
use Modules\Import\DTO\ImportResult;
use Modules\Import\DTO\NormalizedRowDto;
use Modules\Import\DTO\SchemaRowDto;
use Modules\Import\Enums\SchemaCollectionKey;
use Modules\Import\Repositories\ClientsRepository;
use Modules\Import\Repositories\ManufacturersRepository;
use Modules\Import\Repositories\SchemaRowsRepository;
use Modules\Import\Repositories\ProductsRepository;
use Modules\Import\Repositories\SuppliersRepository;
use Modules\Import\Repositories\WarehousesRepository;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use Throwable;
use Modules\Import\Validators\ImportSheetValidator;
use Yii;
use Psr\Log\LoggerInterface;

final class XlsxMongoImportService
{
    /**
     * PhpSpreadsheet is not truly streaming; use chunked reads to cap memory.
     */
    private const READ_CHUNK_SIZE_ROWS = 500;

    public function __construct(
        private readonly SchemaRowsRepository     $rows,
        private readonly MojibakeDecoder          $decoder,
        private readonly ImportSheetValidator     $sheetValidator,
        private readonly ImportRowNormalizer      $normalizer,
        private readonly ManufacturersRepository  $manufacturers,
        private readonly SuppliersRepository      $suppliers,
        private readonly WarehousesRepository     $warehouses,
        private readonly ProductsRepository       $products,
        private readonly ClientsRepository        $clients,
        private readonly LoggerInterface          $logger,
    )
    {
    }

    public function import(ImportRequest $request): ImportResult
    {
        $xlsxPath = Yii::getAlias($request->xlsxPath);
        $reader = $this->createReader();
        $info = $reader->listWorksheetInfo($xlsxPath);
        if ($info === [] || !isset($info[0]['totalRows'])) {
            return new ImportResult(0);
        }
        $totalRows = (int)$info[0]['totalRows'];
        if ($totalRows < 1) {
            return new ImportResult(0);
        }

        $filter = new XlsxChunkReadFilter();
        $reader->setReadFilter($filter);

        $filter->setRows(1, 1);
        $sheet = $reader->load($xlsxPath)->getActiveSheet();
        $rowsIt = $sheet->getRowIterator(1, 1);
        $rowsIt->rewind();
        $headerRowValues = $this->rowToValues($rowsIt->current(), count(ImportSheetValidator::expectedHeaders()));
        $decodedHeaderRow = array_map(
            fn(mixed $v): mixed => is_string($v) ? $this->decoder->decode(trim($v)) : $v,
            $headerRowValues,
        );

        $this->sheetValidator->assertStrictHeaders($decodedHeaderRow);
        $fieldNamesByColIndex = $this->sheetValidator->fieldNamesByColIndex($decodedHeaderRow);
        $colsCount = count($fieldNamesByColIndex);

        $batchRows = [];
        $entityBuffersByUid = [
            SchemaCollectionKey::Manufacturers->value => [],
            SchemaCollectionKey::Suppliers->value => [],
            SchemaCollectionKey::Warehouses->value => [],
            SchemaCollectionKey::Products->value => [],
            SchemaCollectionKey::Clients->value => [],
        ];
        $missingFieldCounts = [];

        $processed = 0;
        $skipped = 0;

        $rowNumber = 2;
        for ($startRow = 2; $startRow <= $totalRows; $startRow += self::READ_CHUNK_SIZE_ROWS) {
            $filter->setRows($startRow, self::READ_CHUNK_SIZE_ROWS);
            $chunkSheet = $reader->load($xlsxPath)->getActiveSheet();
            $endRow = min($totalRows, $startRow + self::READ_CHUNK_SIZE_ROWS - 1);
            $chunkRowsIt = $chunkSheet->getRowIterator($startRow, $endRow);

            foreach ($chunkRowsIt as $row) {
                $rowValues = $this->rowToValues($row, $colsCount);

                $missingRequired = $this->sheetValidator->missingRequiredFieldNames(
                    $rowValues,
                    $fieldNamesByColIndex,
                    ImportSheetValidator::DEFAULT_OPTIONAL_FIELD_NAMES,
                );
                if ($missingRequired !== []) {
                    $skipped++;
                    foreach ($missingRequired as $f) {
                        $missingFieldCounts[$f] = ($missingFieldCounts[$f] ?? 0) + 1;
                    }
                    $this->logger->warning('Skipping row: missing required field(s)', [
                        'rowNumber' => $rowNumber,
                        'missing' => $missingRequired,
                    ]);
                    $rowNumber++;
                    continue;
                }

                [$valuesByCol, $fields] = $this->mapRowValuesToFields($rowValues, $fieldNamesByColIndex);
                $normalizedRows = $this->normalizer->normalize($fields);

                $schemaRow = new SchemaRowDto(
                    id: $this->uniqueId($valuesByCol),
                    fields: $fields,
                    refs: $normalizedRows->rowRefs(),
                );
                $batchRows[] = $this->rows->buildUpsertOp($schemaRow);

                $this->bufferEntityDtosByUid($entityBuffersByUid, $normalizedRows);

                $processed++;
                $rowNumber++;

                if (count($batchRows) >= $request->batchSize) {
                    $this->flushBatches(
                        $batchRows,
                        $entityBuffersByUid,
                        $rowNumber,
                    );
                }
            }
        }

        if ($batchRows !== [] || array_filter($entityBuffersByUid) !== []) {
            $this->flushBatches(
                $batchRows,
                $entityBuffersByUid,
                $rowNumber,
            );
        }

        $this->logger->info('Import finished', [
            'processedRows' => $processed,
            'skippedRows' => $skipped,
            'missingRequiredCounts' => $missingFieldCounts,
        ]);

        return new ImportResult($processed);
    }

    private function createReader(): Xlsx
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(true);

        return $reader;
    }

    private function rowToValues(Row $row, int $columnsCount): array
    {
        $values = array_fill(0, $columnsCount, null);

        $cellIt = $row->getCellIterator();
        $cellIt->setIterateOnlyExistingCells(false);

        $i = 0;
        foreach ($cellIt as $cell) {
            if ($i >= $columnsCount) {
                break;
            }
            $values[$i] = $cell?->getValue();
            $i++;
        }

        return $values;
    }

    private function mapRowValuesToFields(array $rowValues, array $fieldNamesByColIndex): array
    {
        $valuesByCol = [];
        $fields = [];
        foreach ($fieldNamesByColIndex as $i => $fieldName) {
            $v = $rowValues[$i] ?? null;
            $valuesByCol[$i + 1] = $v;
            $fields[$fieldName] = is_string($v) ? $this->decoder->decode(trim($v)) : $v;
        }

        return [$valuesByCol, $fields];
    }

    private function bufferEntityDtosByUid(array &$buffersByCollectionAndUid, NormalizedRowDto $row): void
    {
        $buffersByCollectionAndUid[SchemaCollectionKey::Manufacturers->value][$row->manufacturer->uid] = $row->manufacturer;
        $buffersByCollectionAndUid[SchemaCollectionKey::Suppliers->value][$row->supplier->uid] = $row->supplier;
        $buffersByCollectionAndUid[SchemaCollectionKey::Warehouses->value][$row->warehouse->uid] = $row->warehouse;
        $buffersByCollectionAndUid[SchemaCollectionKey::Products->value][$row->product->uid] = $row->product;
        $buffersByCollectionAndUid[SchemaCollectionKey::Clients->value][$row->client->uid] = $row->client;
    }

    private function flushBatches(
        array &$opsRows,
        array &$entityBuffersByUid,
        int   $rowNumber,
    ): void
    {
        $rowsCount = count($opsRows);
        if ($rowsCount > 0) {
            $this->rows->executeBatch($opsRows, ['ordered' => false]);
            $opsRows = [];
        }

        $reposByCollectionKey = $this->reposByCollectionKey();

        $counts = [
            'schema_rows' => $rowsCount,
        ];
        foreach ($reposByCollectionKey as $collectionKey => $repo) {
            $buf = $entityBuffersByUid[$collectionKey] ?? [];
            $count = count($buf);
            $counts[$collectionKey] = $count;
            if ($count > 0) {
                $repo->upsertBatch(array_values($buf), ['ordered' => false]);
                $entityBuffersByUid[$collectionKey] = [];
            }
        }

        $hadWrites = array_sum($counts) > 0;
        if ($hadWrites) {
            $this->logger->info('Flushed batch', ['atRow' => $rowNumber] + $counts);
        }
    }

    private function reposByCollectionKey(): array
    {
        return [
            SchemaCollectionKey::Manufacturers->value => $this->manufacturers,
            SchemaCollectionKey::Suppliers->value => $this->suppliers,
            SchemaCollectionKey::Warehouses->value => $this->warehouses,
            SchemaCollectionKey::Products->value => $this->products,
            SchemaCollectionKey::Clients->value => $this->clients,
        ];
    }

    private function uniqueId(array $valuesByCol): string
    {
        try {
            return sha1(json_encode($valuesByCol, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR));
        } catch (Throwable) {
            return sha1(serialize($valuesByCol));
        }
    }
}
