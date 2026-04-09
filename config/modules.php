<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;

$configs = [];

foreach ([
             __DIR__ . '/../modules/config/src/Config/common.php',
             __DIR__ . '/../modules/import/src/Config/common.php',
             __DIR__ . '/../modules/import/src/Config/console.php',
             __DIR__ . '/../modules/index/src/Config/common.php',
             __DIR__ . '/../modules/index/src/Config/console.php',
             __DIR__ . '/../modules/aggregation/src/Config/common.php',
             __DIR__ . '/../modules/aggregation/src/Config/console.php',
         ] as $file) {
    if (is_file($file)) {
        $configs[] = require $file;
    }
}

return ArrayHelper::merge(...$configs);

