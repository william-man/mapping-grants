<?php

use Illuminate\Support\Facades\Route;

// There may be security issues for reading php files this way.
// Might be better to just be explicit.
Route::prefix('v1')->group(function () {
    $apiDirectory = new RecursiveDirectoryIterator( __DIR__ . '/api', RecursiveDirectoryIterator::SKIP_DOTS);
    $apiIterator = new RecursiveIteratorIterator($apiDirectory);

    foreach ($apiIterator as $file) {
        if ($file->getExtension() === 'php') {
            try {
                require_once $file->getPathname();
            } catch (Throwable $error) {
                error_log("Error including file: " . $file->getPathname() . " - " . $error->getMessage());
            }
        }
    };
});
