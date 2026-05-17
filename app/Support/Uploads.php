<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class Uploads
{
    public static function disk(): string
    {
        return (string) config('filesystems.uploads_disk', 'public');
    }

    public static function isCloud(): bool
    {
        return config('filesystems.disks.'.self::disk().'.driver') === 's3';
    }

    public static function store($file, string $directory): string
    {
        return $file->store($directory, self::disk());
    }

    public static function delete(?string $path): void
    {
        if (filled($path)) {
            Storage::disk(self::disk())->delete(self::normalizePath($path));
        }
    }

    public static function normalizePath(string $path): string
    {
        return ltrim(str_replace(['public/', 'storage/'], '', $path), '/');
    }

    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $normalized = self::normalizePath($path);

        if (self::isCloud()) {
            return Storage::disk(self::disk())->url($normalized);
        }

        return route('storage.public.show', ['path' => $normalized]);
    }
}
