<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

try {
    // Setup writable storage directory in /tmp for Vercel Serverless
    $tmp = '/tmp';
    $tmpStorage = $tmp . '/storage';
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
    $sqlitePath = $tmp . '/database.sqlite';
    if (!file_exists($sqlitePath)) {
        if (file_exists(__DIR__ . '/../database/database.sqlite')) {
            @copy(__DIR__ . '/../database/database.sqlite', $sqlitePath);
        } else {
            @touch($sqlitePath);
        }
    }
    @chmod($sqlitePath, 0777);

    putenv("DB_DATABASE={$sqlitePath}");
    $_ENV['DB_DATABASE'] = $sqlitePath;
    putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
    $_ENV['VIEW_COMPILED_PATH'] = "{$tmpStorage}/framework/views";

    // Normalize Vercel REQUEST_URI / PATH_INFO so Laravel routes match properly
    if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] === '/api/index.php') {
        $_SERVER['PATH_INFO'] = '/';
    }
    if (isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], '/api/index.php')) {
        $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen('/api/index.php')) ?: '/';
    }

    require __DIR__ . '/../vendor/autoload.php';

    /** @var Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath($tmpStorage);

    // Render detailed exception directly to browser for debugging
    $app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class)->renderable(function (\Throwable $e) {
        http_response_code(500);
        echo "<h1>Original Exception</h1>";
        echo "<p><strong>" . get_class($e) . ":</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        exit;
    });

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Laravel Server Error</h1>";
    $curr = $e;
    while ($curr) {
        echo "<h3>" . get_class($curr) . ": " . htmlspecialchars($curr->getMessage()) . "</h3>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($curr->getFile()) . ":" . $curr->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($curr->getTraceAsString()) . "</pre><hr>";
        $curr = $curr->getPrevious();
    }
}
