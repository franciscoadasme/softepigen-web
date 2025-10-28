<?php

namespace App\Helpers;

class FASTAHelper
{
    public static function isValid(string $path): bool
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            return false;
        }

        $isValid = false;
        while (($line = fgets($handle, 1024)) !== false) {
            $line = trim($line);
            if (!$line) {
                continue;
            }

            if (!str_starts_with($line, '>')) {
                break;
            }

            $line = fgets($handle, 1024) ?? '';
            $isValid = (bool) preg_match(
                '/^[ACGTURYKMSWBDHVN-]+$/i',
                trim($line),
            );
            break;
        }

        fclose($handle);
        return $isValid;
    }
}
