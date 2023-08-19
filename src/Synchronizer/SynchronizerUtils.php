<?php

namespace App\Synchronizer;

class SynchronizerUtils
{
    public static function array_value_as_int(array $data, string $key): ?int
    {
        return isset($data[$key]) ? (int) $data[$key] : null;
    }

    public static function array_value_as_string(array $data, string $key): ?string
    {
        return isset($data[$key]) ? (string) $data[$key] : null;
    }

    public static function array_value_as_bool(array $data, string $key): ?bool
    {
        return isset($data[$key]) ? (bool) $data[$key] : null;
    }

    public static function array_amount_value_as_int(array $data, string $key = 'amount'): ?int
    {
        return isset($data[$key]) ? (int) \str_replace('x', '', $data[$key]) : null;
    }
}
