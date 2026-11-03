<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', fn () => view('welcome'));
Route::get('/ar', fn () => view('welcome'));
Route::get('/en', fn () => view('welcome'));

/**
 * Serve private quotation attachments to authenticated Filament admins.
 *
 * URL: GET /admin/quotation-files/{quotation}/{filename}
 * Auth: Uses Filament's own panel auth guard (session-based).
 */
Route::middleware(['web', 'auth', 'can:view,quotation'])->group(function () {
    Route::get('/admin/quotation-files/{quotation}/{filename}', function (
        \App\Models\Quotation $quotation,
        string $filename
    ) {
        // Reject any path-traversal attempts.
        if (str_contains($filename, '..') || str_contains($filename, '/')) {
            abort(403);
        }

        $path = 'quotations/' . $filename;

        // Confirm the file is listed in this quotation's files column.
        $allowed = collect($quotation->files ?? [])->map(fn ($f) => basename($f));
        if (! $allowed->contains($filename)) {
            abort(403);
        }

        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->download(Storage::disk('local')->path($path), $filename);
    })->name('admin.quotation.file.download');
});
