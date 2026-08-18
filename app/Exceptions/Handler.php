<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Render error sebagai JSON saat request datang dari AJAX / expects JSON.
        // Ini mencegah halaman HTML error (mis. debug page saat APP_DEBUG=true)
        // bocor ke handler JS `.lp-ajax` yang selalu set 'X-Requested-With: XMLHttpRequest'
        // + 'Accept: application/json'.
        $this->renderable(function (Throwable $e, Request $request) {
            if (! $request->expectsJson() && ! $request->ajax() && ! $request->headers->has('X-Requested-With')) {
                return null; // biarkan default (HTML) untuk request biasa
            }

            // Tentukan status code yang tepat per jenis exception.
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $status = 422;
            } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                $status = 401;
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $status = $e->getStatusCode();
            } elseif (method_exists($e, 'getStatusCode')) {
                $status = $e->getStatusCode();
            } else {
                $status = 500;
            }
            if ($status < 400 || $status > 599) {
                $status = 500;
            }

            $msg = $e->getMessage() ?: 'Terjadi kesalahan pada server.';
            // Untuk ValidationException, sertakan daftar error per field agar form bisa highlight.
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $errors = $e->errors();
                $msg = collect($errors)->flatten()->first() ?: $msg;
                $payload = [
                    'success' => false,
                    'msg'     => $msg,
                    'errors'  => $errors,
                    'error'   => 'ValidationException',
                ];
            } else {
                $payload = [
                    'success' => false,
                    'msg'     => $msg,
                    'error'   => class_basename($e),
                ];
            }

            // Saat debug, sertakan file + baris supaya DevTools / log lebih informatif.
            if (config('app.debug')) {
                $payload['debug'] = [
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                    'trace' => collect($e->getTrace())->take(8)->map(function ($t) {
                        return ($t['file'] ?? '?') . ':' . ($t['line'] ?? '?');
                    })->values(),
                ];
            }

            return response()->json($payload, $status);
        });
    }
}
