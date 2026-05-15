<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class ItemsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private int $importedCount = 0;
    private array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because row 1 is the header

            // Normalise keys: support both "total_stock" and "total stock"
            $name        = trim($row['name'] ?? '');
            $category    = trim($row['category'] ?? '');
            $location    = trim($row['location'] ?? '');
            $description = trim($row['description'] ?? '');
            $totalStock  = (int) ($row['total_stock'] ?? $row['total stock'] ?? 0);
            $availRaw    = $row['available_stock'] ?? $row['available stock'] ?? null;
            $availableStock = ($availRaw !== null && $availRaw !== '')
                ? (int) $availRaw
                : $totalStock;

            // Manual validation
            $rowErrors = [];
            if ($name === '')       $rowErrors[] = 'name is required';
            if ($category === '')   $rowErrors[] = 'category is required';
            if ($location === '')   $rowErrors[] = 'location is required';
            if ($totalStock < 1)    $rowErrors[] = 'total_stock must be at least 1';

            if (!empty($rowErrors)) {
                $this->errors[] = "Row {$rowNum}: " . implode(', ', $rowErrors) . '.';
                continue;
            }

            $item = Item::create([
                'name'            => $name,
                'description'     => $description,
                'category'        => $category,
                'location'        => $location,
                'total_stock'     => $totalStock,
                'available_stock' => min($availableStock, $totalStock),
            ]);

            // Auto-assign QR code
            $item->update([
                'qr_code' => 'SEIMS-' . str_pad($item->id, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(8)),
            ]);

            // Create individual units
            $pad = str_pad($item->id, 6, '0', STR_PAD_LEFT);
            for ($i = 1; $i <= $totalStock; $i++) {
                $unitCode = "SEIMS-{$pad}-U" . str_pad($i, 3, '0', STR_PAD_LEFT);
                ItemUnit::create([
                    'item_id'   => $item->id,
                    'unit_code' => $unitCode,
                    'qr_code'   => $unitCode . '-' . strtoupper(Str::random(6)),
                    'status'    => 'available',
                    'condition' => 'good',
                ]);
            }

            $this->importedCount++;
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
