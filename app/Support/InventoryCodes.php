<?php

namespace App\Support;

use Illuminate\Support\Str;

class InventoryCodes
{
    public static function prefix(): string
    {
        return (string) config('app.inventory_code_prefix', 'SEIS');
    }

    public static function itemQrCode(int $itemId): string
    {
        $pad = str_pad((string) $itemId, 6, '0', STR_PAD_LEFT);

        return static::prefix() . '-' . $pad . '-' . strtoupper(Str::random(8));
    }

    public static function unitCode(int $itemId, int $sequence): string
    {
        $pad = str_pad((string) $itemId, 6, '0', STR_PAD_LEFT);

        return static::prefix() . "-{$pad}-U" . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
