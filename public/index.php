<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Early fatal-error capture (DEBUG ONLY)
|--------------------------------------------------------------------------
|
| Tangkap error fatal (yang biasanya tidak masuk Laravel log) ke file khusus
| supaya kita bisa lihat error aslinya. Hanya aktif saat APP_DEBUG=true
| dan kalau env marker ENABLE_FATAL_CAPTURE=1.
|
*/

// Baca .env langsung agar fatal-capture aktif bahkan sebelum Laravel bootstrap.
$_fatalEnvFile = __DIR__ . '/../.env';
$_fatalDebug = false;
$_fatalEnable = false;
if (is_readable($_fatalEnvFile)) {
    foreach (file($_fatalEnvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_fatalLine) {
        if (str_starts_with(ltrim($_fatalLine), '#')) continue;
        if (preg_match('/^\s*APP_DEBUG\s*=\s*(.+?)\s*$/', $_fatalLine, $m)) {
            $_fatalDebug = in_array(strtolower(trim($m[1], " \"'")), ['true', '1', 'yes', 'on'], true);
        }
        if (preg_match('/^\s*ENABLE_FATAL_CAPTURE\s*=\s*(.+?)\s*$/', $_fatalLine, $m)) {
            $_fatalEnable = in_array(strtolower(trim($m[1], " \"'")), ['1', 'true', 'yes', 'on'], true);
        }
    }
}
if (PHP_SAPI !== 'cli' && ($_fatalEnable || $_fatalDebug)) {
    $fatalLog = __DIR__ . '/../storage/logs/fatal-errors.log';
    @ini_set('display_errors', '0'); // Memblokir HTML error page generik Apache.

    register_shutdown_function(function () use ($fatalLog) {
        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
            $line = '[' . date('Y-m-d H:i:s') . '] ' . $err['message']
                . ' in ' . $err['file'] . ':' . $err['line'] . PHP_EOL;
            @file_put_contents($fatalLog, $line, FILE_APPEND);

            // Untuk AJAX, kirim JSON error 500 agar handler JS bisa menampilkan toast.
            if (! headers_sent() && (
                (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
            )) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'msg' => 'Fatal: ' . $err['message'] . ' (' . basename($err['file']) . ':' . $err['line'] . ')',
                    'error' => 'FatalError',
                    'debug' => ['file' => $err['file'], 'line' => $err['line']],
                ]);
                exit;
            }
        }
    });
}

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so that we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy it.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
