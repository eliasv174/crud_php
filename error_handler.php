<?php
declare(strict_types=1);

function register_custom_error_handler(): void
{
    set_error_handler(function ($severity, $message, $file, $line): bool {
        $logMessage = sprintf(
            '[%s] %s: %s in %s on line %d',
            date('c'),
            $severity,
            $message,
            $file,
            $line
        );

        error_log($logMessage);

        if (http_response_code() === 200) {
            http_response_code(500);
        }

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
        }

        echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Error interno</title></head><body style="font-family:Arial,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f8f9fa;color:#212529;">';
        echo '<div style="text-align:center;padding:2rem;background:#fff;border:1px solid #ddd;border-radius:8px;max-width:32rem;">';
        echo '<h1 style="font-size:1.5rem;margin-bottom:0.75rem;">Ocurrió un error interno</h1>';
        echo '<p style="margin:0;">Inténtelo nuevamente más tarde.</p>';
        echo '</div></body></html>';

        return true;
    }, E_ALL);

    set_exception_handler(function (Throwable $exception): void {
        $logMessage = sprintf(
            '[%s] Uncaught exception: %s in %s on line %d',
            date('c'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        error_log($logMessage);
        http_response_code(500);

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
        }

        echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Error interno</title></head><body style="font-family:Arial,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f8f9fa;color:#212529;">';
        echo '<div style="text-align:center;padding:2rem;background:#fff;border:1px solid #ddd;border-radius:8px;max-width:32rem;">';
        echo '<h1 style="font-size:1.5rem;margin-bottom:0.75rem;">Ocurrió un error interno</h1>';
        echo '<p style="margin:0;">Inténtelo nuevamente más tarde.</p>';
        echo '</div></body></html>';
        exit;
    });

    register_shutdown_function(function (): void {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
            error_log(sprintf(
                '[%s] Fatal error: %s in %s on line %d',
                date('c'),
                $error['message'],
                $error['file'],
                $error['line']
            ));

            http_response_code(500);
            if (!headers_sent()) {
                header('Content-Type: text/html; charset=UTF-8');
            }

            echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Error interno</title></head><body style="font-family:Arial,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f8f9fa;color:#212529;">';
            echo '<div style="text-align:center;padding:2rem;background:#fff;border:1px solid #ddd;border-radius:8px;max-width:32rem;">';
            echo '<h1 style="font-size:1.5rem;margin-bottom:0.75rem;">Ocurrió un error interno</h1>';
            echo '<p style="margin:0;">Inténtelo nuevamente más tarde.</p>';
            echo '</div></body></html>';
        }
    });
}
