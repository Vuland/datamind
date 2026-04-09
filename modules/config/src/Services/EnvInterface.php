<?php

declare(strict_types=1);

namespace Modules\Config\Services;

use Modules\Config\Enums\EnvKey;

interface EnvInterface
{
    public function get(string|EnvKey $key, ?string $default = null): string;
}
