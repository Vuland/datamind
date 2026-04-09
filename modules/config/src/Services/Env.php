<?php

declare(strict_types=1);

namespace Modules\Config\Services;

use Modules\Config\Enums\EnvKey;

final class Env implements EnvInterface
{
    public function get(string|EnvKey $key, ?string $default = null): string
    {
        [$k, $d] = $this->resolveKeyAndDefault($key, $default);
        $v = $this->raw($k);
        if ($v === null || $v === '') {
            return $d;
        }

        return (string)$v;
    }

    private function resolveKeyAndDefault(string|EnvKey $key, ?string $default): array
    {
        if ($key instanceof EnvKey) {
            return [$key->value, $default ?? $key->defaultValue()];
        }

        return [$key, $default ?? ''];
    }

    private function raw(string $key): string|int|float|bool|null
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }
        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }
        $g = getenv($key);

        return $g === false ? null : $g;
    }
}
