<?php

declare(strict_types=1);

namespace Modules\Import\Components;

/**
 * External / third-party-style component bundled for convenience — not authored as part of this
 * application’s domain logic. Repairs typical Excel / PhpSpreadsheet mojibake (UTF-8 bytes shown
 * as Windows-1252/Latin-1 glyphs).
 */
final class MojibakeDecoder
{
    private static ?array $cp1252Utf8ToByte = null;

    private static ?array $latin1Utf8ToByte = null;

    public function decode(string $s): string
    {
        if ($s === '') {
            return $s;
        }

        if (!str_contains($s, 'Ð') && !str_contains($s, 'Ñ')) {
            return $s;
        }

        $bytes = $this->mojibakeUtf8ToRawBytes($s);
        if ($bytes === null) {
            return $s;
        }

        $bytes = $this->repairCp1252SkippedContinuation($bytes);

        $bytes = $this->stripTrailingIncompleteUtf8($bytes);
        if ($bytes === '') {
            return $s;
        }

        $fixed = @iconv('UTF-8', 'UTF-8//IGNORE', $bytes);
        if (!is_string($fixed) || $fixed === '') {
            return $s;
        }

        if (str_contains($fixed, 'Ð') || str_contains($fixed, 'Ñ')) {
            return $s;
        }

        return mb_check_encoding($fixed, 'UTF-8') ? $fixed : $s;
    }

    /**
     * Each original UTF-8 byte was shown as one Windows-1252 character (UTF-8-encoded in the cell).
     * CP1252 leaves holes (e.g. 0x81); those bytes appear as ISO-8859-1 control chars (e.g. U+0081 → C2 81).
     */
    private function mojibakeUtf8ToRawBytes(string $s): ?string
    {
        $cp = self::cp1252Utf8ToByteMap();
        $l1 = self::latin1Utf8ToByteMap();

        $glyphs = preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY);
        if ($glyphs === false) {
            return null;
        }

        $bytes = '';
        foreach ($glyphs as $g) {
            if (isset($cp[$g])) {
                $bytes .= chr($cp[$g]);
                continue;
            }
            if (isset($l1[$g])) {
                $bytes .= chr($l1[$g]);
                continue;
            }

            return null;
        }

        return $bytes;
    }

    private static function cp1252Utf8ToByteMap(): array
    {
        if (self::$cp1252Utf8ToByte !== null) {
            return self::$cp1252Utf8ToByte;
        }

        $map = [];
        for ($b = 0; $b < 256; $b++) {
            $u = @iconv('Windows-1252', 'UTF-8', chr($b));
            if (!is_string($u) || $u === '') {
                continue;
            }
            $map[$u] = $b;
        }

        self::$cp1252Utf8ToByte = $map;

        return $map;
    }

    private static function latin1Utf8ToByteMap(): array
    {
        if (self::$latin1Utf8ToByte !== null) {
            return self::$latin1Utf8ToByte;
        }

        $map = [];
        for ($b = 0; $b < 256; $b++) {
            $u = @iconv('ISO-8859-1', 'UTF-8', chr($b));
            if (!is_string($u) || $u === '') {
                continue;
            }
            $map[$u] = $b;
        }

        self::$latin1Utf8ToByte = $map;

        return $map;
    }

    /**
     * Bytes 0x81 are not valid as single CP1252 code points; readers often drop them.
     * That merges two UTF-8 lead bytes into 0xD1 0xD1; insert the missing 0x81 between them.
     */
    private function repairCp1252SkippedContinuation(string $bytes): string
    {
        while (($p = strpos($bytes, "\xD1\xD1")) !== false) {
            $bytes = substr_replace($bytes, "\xD1\x81\xD1", $p, 2);
        }

        return $bytes;
    }

    /**
     * Excel / PhpSpreadsheet sometimes truncates a cell mid–UTF-8 sequence.
     */
    private function stripTrailingIncompleteUtf8(string $bytes): string
    {
        $len = strlen($bytes);
        if ($len === 0) {
            return $bytes;
        }

        $i = 0;
        while ($i < $len) {
            $c = ord($bytes[$i]);
            if ($c < 0x80) {
                $i++;
                continue;
            }

            if (($c & 0xE0) === 0xC0) {
                $charLen = 2;
            } elseif (($c & 0xF0) === 0xE0) {
                $charLen = 3;
            } elseif (($c & 0xF8) === 0xF0) {
                $charLen = 4;
            } else {
                return substr($bytes, 0, $i);
            }

            if ($i + $charLen > $len) {
                return substr($bytes, 0, $i);
            }

            for ($j = 1; $j < $charLen; $j++) {
                if ((ord($bytes[$i + $j]) & 0xC0) !== 0x80) {
                    return substr($bytes, 0, $i);
                }
            }

            $i += $charLen;
        }

        return $bytes;
    }
}
