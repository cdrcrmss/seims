<?php

namespace App\Http\Controllers;

use App\Support\Uploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicFileController extends Controller
{
    /**
     * Serve uploads locally; redirect to cloud URL when using object storage.
     */
    public function show(string $path)
    {
        $path = str_replace(['\\', '..'], '', $path);

        if (! Str::startsWith($path, ['items/', 'return-images/'])) {
            abort(404);
        }

        $disk = Uploads::disk();

        if (Uploads::isCloud()) {
            if (! Storage::disk($disk)->exists($path)) {
                abort(404);
            }

            return redirect(Storage::disk($disk)->url($path));
        }

        if (! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        return Storage::disk($disk)->response($path);
    }
}
