<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemUnit;
use App\Support\InventoryCodes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ItemsImport
{
    private int $importedCount = 0;

    private array $errors = [];

    private const REQUIRED_COLUMNS = ['name', 'category', 'total_stock', 'location', 'laboratory'];

    public function importFromUpload(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['csv', 'txt'])) {
            $this->importCsv($file);
        } else {
            $this->importSpreadsheet($file);
        }
    }

    private function importSpreadsheet(UploadedFile $file): void
    {
        $sheet = Excel::toArray(null, $file)[0] ?? [];

        if (empty($sheet)) {
            $this->errors[] = 'File is empty or invalid.';

            return;
        }

        $headerRow = array_shift($sheet);
        $this->processDataRows($headerRow, $sheet);
    }

    private function importCsv(UploadedFile $file): void
    {
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            $this->errors[] = 'Could not read the file.';

            return;
        }

        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);
            $this->errors[] = 'File is empty or invalid.';

            return;
        }

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = $data;
        }

        fclose($handle);

        $this->processDataRows($header, $rows);
    }

    private function processDataRows(array $headerRow, array $dataRows): void
    {
        $headers = $this->normalizeHeaders($headerRow);

        foreach (self::REQUIRED_COLUMNS as $col) {
            if (!in_array($col, $headers, true)) {
                $this->errors[] = 'File is missing required column: ' . $col;

                return;
            }
        }

        foreach ($dataRows as $index => $row) {
            $rowNum = $index + 2;

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $expectedColumns = count(array_filter($headers, fn ($h) => $h !== ''));
            if (count($row) < $expectedColumns) {
                $this->errors[] = "Row {$rowNum}: Column count mismatch.";

                continue;
            }

            $rowData = $this->combineRow($headers, $row);

            $this->processRow($rowNum, $rowData);
        }
    }

    private function normalizeHeaders(array $headerRow): array
    {
        return array_map(function ($h) {
            $key = strtolower(trim((string) $h));
            $key = preg_replace('/^\x{FEFF}/u', '', $key);
            $key = preg_replace('/[\s\-]+/', '_', $key);

            return trim($key, '_');
        }, $headerRow);
    }

    private function combineRow(array $headers, array $row): array
    {
        $data = [];
        foreach ($headers as $i => $key) {
            if ($key === '') {
                continue;
            }
            $data[$key] = $row[$i] ?? null;
        }

        return $data;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function processRow(int $rowNum, array $row): void
    {
        $name        = trim((string) ($row['name'] ?? ''));
        $category    = trim((string) ($row['category'] ?? ''));
        $location    = trim((string) ($row['location'] ?? ''));
        $laboratory  = trim((string) ($row['laboratory'] ?? ''));
        $description = trim((string) ($row['description'] ?? ''));
        $totalStock  = (int) ($row['total_stock'] ?? 0);
        $availRaw    = $row['available_stock'] ?? null;
        $availableStock = ($availRaw !== null && $availRaw !== '')
            ? (int) $availRaw
            : $totalStock;

        $rowErrors = [];
        if ($name === '') {
            $rowErrors[] = 'name is required';
        }
        if ($category === '') {
            $rowErrors[] = 'category is required';
        }
        if ($location === '') {
            $rowErrors[] = 'location is required';
        }
        if ($laboratory === '') {
            $rowErrors[] = 'laboratory is required';
        }
        if ($totalStock < 1) {
            $rowErrors[] = 'total_stock must be at least 1';
        }

        if (!empty($rowErrors)) {
            $this->errors[] = "Row {$rowNum}: " . implode(', ', $rowErrors) . '.';

            return;
        }

        $existing = Item::where('name', $name)->where('category', $category)->first();

        if ($existing) {
            $existingUnitCount = $existing->units()->count();
            $existing->increment('total_stock', $totalStock);
            $existing->increment('available_stock', min($availableStock, $totalStock));

            $existing->fill(array_filter([
                'location'    => $existing->location ?: $location,
                'laboratory'  => $existing->laboratory ?: $laboratory,
                'description' => $existing->description ?: $description,
            ]))->save();

            $pad = str_pad($existing->id, 6, '0', STR_PAD_LEFT);
            for ($i = 1; $i <= $totalStock; $i++) {
                $seq = $existingUnitCount + $i;
                $unitCode = InventoryCodes::unitCode($existing->id, $seq);
                ItemUnit::create([
                    'item_id'   => $existing->id,
                    'unit_code' => $unitCode,
                    'qr_code'   => $unitCode . '-' . strtoupper(Str::random(6)),
                    'status'    => 'available',
                    'condition' => 'good',
                ]);
            }

            $this->importedCount++;

            return;
        }

        $item = Item::create([
            'name'            => $name,
            'description'     => $description,
            'category'        => $category,
            'location'        => $location,
            'laboratory'      => $laboratory,
            'total_stock'     => $totalStock,
            'available_stock' => min($availableStock, $totalStock),
        ]);

        $item->update([
            'qr_code' => InventoryCodes::itemQrCode($item->id),
        ]);

        for ($i = 1; $i <= $totalStock; $i++) {
            $unitCode = InventoryCodes::unitCode($item->id, $i);
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

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
