<?php

namespace App\Synchronizer;

class SynchronizerUtils
{
    public static function array_value_to_int(array $data, string $key): ?int
    {
        return isset($data[$key]) ? (int) $data[$key] : null;
    }
}
