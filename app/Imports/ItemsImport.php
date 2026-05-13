<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;

class ItemsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors, Importable;

    private int $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $totalStock = (int) ($row['total_stock'] ?? $row['total stock'] ?? 0);
            $availableStock = (int) ($row['available_stock'] ?? $row['available stock'] ?? $totalStock);

            $item = Item::create([
                'name' => trim($row['name'] ?? ''),
                'description' => trim($row['description'] ?? ''),
                'category' => trim($row['category'] ?? ''),
                'total_stock' => $totalStock,
                'available_stock' => min($availableStock, $totalStock),
            ]);

            // Auto-assign QR code
            $item->update(['qr_code' => 'SEIMS-' . str_pad($item->id, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(8))]);

            // Create individual units for tracking
            $itemPad = str_pad($item->id, 6, '0', STR_PAD_LEFT);
            for ($i = 1; $i <= $totalStock; $i++) {
                $unitCode = "SEIMS-{$itemPad}-U" . str_pad($i, 3, '0', STR_PAD_LEFT);
                \App\Models\ItemUnit::create([
                    'item_id' => $item->id,
                    'unit_code' => $unitCode,
                    'qr_code' => $unitCode . '-' . strtoupper(Str::random(6)),
                    'status' => 'available',
                    'condition' => 'good',
                ]);
            }

            $this->importedCount++;
        }
    }

    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:255',
            '*.category' => 'required|string|max:255',
            '*.total_stock' => 'required|integer|min:1',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.name.required' => 'Item name is required.',
            '*.category.required' => 'Category is required.',
            '*.total_stock.required' => 'Total stock is required.',
            '*.total_stock.min' => 'Total stock must be at least 1.',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
