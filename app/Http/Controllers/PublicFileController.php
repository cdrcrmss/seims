<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicFileController extends Controller
{
    /**
     * Serve files from the public storage disk (item photos, return images, etc.).
     */
    public function show(string $path)
    {
        $path = str_replace(['\\', '..'], '', $path);

        if (! Str::startsWith($path, ['items/', 'return-images/'])) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
}
