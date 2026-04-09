<?php

declare(strict_types=1);

namespace Modules\Aggregation;

use yii\base\Module as BaseModule;

final class Module extends BaseModule
{
    public const ID = 'aggregation';

    public $controllerNamespace = 'Modules\\Aggregation\\Console\\Controllers';
}

