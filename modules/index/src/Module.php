<?php

declare(strict_types=1);

namespace Modules\Index;

use yii\base\Module as BaseModule;

final class Module extends BaseModule
{
    public const ID = 'index';

    public $controllerNamespace = 'Modules\\Index\\Console\\Controllers';
}

