<?php

declare(strict_types=1);

namespace Modules\Config\Enums;

enum EnvKey: string
{
    case XlsxPath = 'XLSX_PATH';
    case MongoDsn = 'MONGO_DSN';
    case MongoDb = 'MONGO_DB';
    case EsHost = 'ES_HOST';
    case EsIndex = 'ES_INDEX';

    public function defaultValue(): string
    {
        return match ($this) {
            self::XlsxPath => '@app/data/input.xlsx',
            self::MongoDsn => 'mongodb://127.0.0.1:27017',
            self::MongoDb => 'datamind',
            self::EsHost => 'http://127.0.0.1:9200',
            self::EsIndex => 'datamind_rows',
        };
    }
}

