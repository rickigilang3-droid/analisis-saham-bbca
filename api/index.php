<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

// Setup writable storage directory in /tmp for Vercel Serverless
$tmpStorage = '/tmp/storage';
if (!is_dir($tmpStorage . '/framework/views')) {
    @mkdir($tmpStorage . '/framework/views', 0777, true);
    @mkdir($tmpStorage . '/framework/cache', 0777, true);
    @mkdir($tmpStorage . '/framework/sessions', 0777, true);
    @mkdir($tmpStorage . '/logs', 0777, true);
}

// Setup SQLite in /tmp
$sqlitePath = '/tmp/database.sqlite';
$isFirstInit = !file_exists($sqlitePath);
if ($isFirstInit) {
    @touch($sqlitePath);
}

putenv("DB_DATABASE={$sqlitePath}");
$_ENV['DB_DATABASE'] = $sqlitePath;
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
$_ENV['VIEW_COMPILED_PATH'] = "{$tmpStorage}/framework/views";

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath($tmpStorage);

if ($isFirstInit) {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
    } catch (\Throwable $e) {
        // Continue if already migrated
    }
}

$app->handleRequest(Request::capture());
