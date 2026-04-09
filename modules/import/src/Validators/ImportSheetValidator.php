<?php

declare(strict_types=1);

namespace Modules\Import\Validators;

use InvalidArgumentException;
use Yii;

final class ImportSheetValidator
{
    public const DEFAULT_OPTIONAL_FIELD_NAMES = [
        'delivery_address',
        'legal_address',
        'client_division_code',
        'client_okpo',
        'license',
        'license_expires_at',
        'product_barcode',
        'morion_code',
    ];

    public function fieldNamesByColIndex(array $headerRow): array
    {
        $this->assertStrictHeaders($headerRow);

        $map = self::headerToFieldNameMap();
        $headers = array_map(
            static fn(mixed $v): string => trim((string)$v),
            $headerRow
        );

        return array_map(
            static fn(string $h): string => $map[$h],
            $headers
        );
    }

    public function assertStrictHeaders(array $headerRow): void
    {
        $expectedHeaders = self::expectedHeaders();

        $actualHeaders = array_map(
            static fn(mixed $v): string => trim((string)$v),
            $headerRow
        );

        if ($actualHeaders !== $expectedHeaders) {
            Yii::warning(
                'XLSX headers mismatch',
                [
                    'expected' => $expectedHeaders,
                    'actual' => $actualHeaders,
                ]
            );
            throw new InvalidArgumentException('Invalid XLSX header structure');
        }
    }

    public function missingRequiredFieldNames(array $rowValues, array $fieldNamesByColIndex, array $optionalFieldNames = []): array
    {
        $optional = array_fill_keys($optionalFieldNames, true);
        $missing = [];

        foreach ($fieldNamesByColIndex as $i => $fieldName) {
            if (isset($optional[$fieldName])) {
                continue;
            }

            $v = $rowValues[$i] ?? null;
            if ($v === null) {
                $missing[] = $fieldName;
                continue;
            }
            if (is_string($v) && trim($v) === '') {
                $missing[] = $fieldName;
            }
        }

        return $missing;
    }

    public static function expectedHeaders(): array
    {
        return array_keys(self::headerToFieldNameMap());
    }

    private static function headerToFieldNameMap(): array
    {
        return [
            'Фирма' => 'firm',
            'Область' => 'area',
            'Город' => 'city',
            'Дата накл' => 'invoice_date',
            'Факт.адрес доставки' => 'delivery_address',
            'Юр. адрес клиента' => 'legal_address',
            'Клиент' => 'client',
            'Код клиента' => 'client_code',
            'Код подразд кл' => 'client_division_code',
            'ОКПО клиента' => 'client_okpo',
            'Лицензия' => 'license',
            'Дата окончания лицензии' => 'license_expires_at',
            'Код товара' => 'product_code',
            'Штрих-код товара' => 'product_barcode',
            'Товар' => 'product',
            'Код мориона' => 'morion_code',
            'ЕИ' => 'unit',
            'Производитель' => 'manufacturer',
            'Поставщик' => 'supplier',
            'Количество' => 'quantity',
            'Склад/филиал' => 'warehouse',
        ];
    }
}

