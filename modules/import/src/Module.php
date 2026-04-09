<?php

declare(strict_types=1);

namespace Modules\Import;

use yii\base\Module as BaseModule;

final class Module extends BaseModule
{
    public const ID = 'import';

    public $controllerNamespace = 'Modules\\Import\\Console\\Controllers';
}

