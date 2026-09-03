<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

try {
    // Setup writable storage directory in /tmp for Vercel Serverless
    $tmpStorage = '/tmp/storage';
    foreach ([
        $tmpStorage . '/framework/views',
        $tmpStorage . '/framework/cache',
        $tmpStorage . '/framework/sessions',
        $tmpStorage . '/logs',
        $tmpStorage . '/app/public'
    ] as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    // Setup SQLite in /tmp
    $sqlitePath = '/tmp/database.sqlite';
    $isFirstInit = !file_exists($sqlitePath);
    if ($isFirstInit) {
        if (file_exists(__DIR__ . '/../database/database.sqlite')) {
            @copy(__DIR__ . '/../database/database.sqlite', $sqlitePath);
        } else {
            @touch($sqlitePath);
        }
    }

    putenv("DB_DATABASE={$sqlitePath}");
    $_ENV['DB_DATABASE'] = $sqlitePath;
    putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
    $_ENV['VIEW_COMPILED_PATH'] = "{$tmpStorage}/framework/views";

    require __DIR__ . '/../vendor/autoload.php';

    /** @var Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath($tmpStorage);

    if ($isFirstInit && !file_exists(__DIR__ . '/../database/database.sqlite')) {
        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        } catch (\Throwable $migEx) {
            error_log("Migration notice: " . $migEx->getMessage());
        }
    }

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Laravel Server Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
