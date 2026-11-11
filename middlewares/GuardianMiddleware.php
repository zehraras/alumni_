<?php
/**
 * GuardianMiddleware
 * 
 * Handles:
 * - Step 4: 404 Guardian Middleware (returns 404 errors with a clear reason)
 * - Step 5: 5xx Guardian Middleware (catches unhandled exceptions and server errors)
 * - Step 6: Logs errors to logs/error.logs file
 * - Step 7: Generic 404 HTML Page renderer for browser requests
 */
class GuardianMiddleware {
    private static string $logFile = __DIR__ . '/../logs/error.logs';

    /**
     * Initialize error logging directory and register exception handlers
     */
    public static function register(): void {
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        if (!file_exists(self::$logFile)) {
            file_put_contents(self::$logFile, "# Error Log File Initialized\n");
        }

        // Set exception handler for 5xx Guardian
        set_exception_handler([__CLASS__, 'handleException']);
        
        // Set error handler to convert notices/warnings into ErrorExceptions when critical
        set_error_handler(function($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        // Set fatal error handler
        register_shutdown_function([__CLASS__, 'handleShutdown']);
    }

    /**
     * Step 6: Log error to logs/error.logs
     */
    public static function logError(int $statusCode, string $message, string $file = '', int $line = 0): void {
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $time = date('Y-m-d H:i:s');
        $method = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $logEntry = sprintf(
            "[%s] %d %s %s | IP: %s | Message: %s | File: %s:%d\n",
            $time,
            $statusCode,
            $method,
            $uri,
            $ip,
            $message,
            $file,
            $line
        );

        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Step 4: 404 Guardian
     */
    public static function handle404(string $path, string $reason = 'The requested endpoint or resource was not found on this server.'): void {
        http_response_code(404);
        
        $isApi = str_starts_with($path, '/api') || 
                 (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

        // Log 404 in error logs as well
        self::logError(404, "Route Not Found: $reason");

        if ($isApi) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 404,
                'error' => 'Not Found',
                'reason' => $reason,
                'path' => $path,
                'timestamp' => date('c')
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Step 7: Render generic 404 HTML view
        self::render404View($path, $reason);
        exit;
    }

    /**
     * Step 5: 5xx Guardian (Exception Handler)
     */
    public static function handleException(Throwable $e): void {
        http_response_code(500);

        // Step 6: Write to logs/error.logs
        self::logError(500, $e->getMessage(), $e->getFile(), $e->getLine());

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $isApi = str_starts_with($path, '/api') || 
                 (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

        if ($isApi) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 500,
                'error' => 'Internal Server Error',
                'reason' => $e->getMessage(),
                'file' => basename($e->getFile()) . ':' . $e->getLine(),
                'timestamp' => date('c')
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }

        // HTML 500 Error
        header('Content-Type: text/html; charset=utf-8');
        echo <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>500 - Sunucu Hatası | Alumni System</title>
    <style>
        body { font-family: -apple-system, sans-serif; background: #f8fafc; color: #0f172a; display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; }
        .card { background:#fff; border:1px solid #fee2e2; border-radius:12px; padding:2.5rem; max-width:550px; text-align:center; box-shadow:0 10px 15px -3px rgba(0,0,0,0.05); }
        h1 { color: #dc2626; margin:0 0 0.5rem; font-size:3rem; }
        p { color: #64748b; line-height:1.6; }
        .btn { display:inline-block; margin-top:1.5rem; background:#2563eb; color:#fff; text-decoration:none; padding:0.6rem 1.2rem; border-radius:6px; font-weight:500; }
    </style>
</head>
<body>
    <div class="card">
        <h1>500</h1>
        <h2>Sunucu Hatası (5xx Guardian)</h2>
        <p>İşleminiz gerçekleştirilirken beklenmeyen bir hata meydana geldi. Bu hata log dosyasına kaydedildi.</p>
        <p style="background:#fef2f2; color:#991b1b; padding:0.5rem; border-radius:4px; font-size:0.85rem; font-family:monospace;">
            {$e->getMessage()}
        </p>
        <a href="/" class="btn">&larr; Ana Sayfaya Dön</a>
    </div>
</body>
</html>
HTML;
        exit;
    }

    /**
     * Handle fatal PHP errors at shutdown
     */
    public static function handleShutdown(): void {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::logError(500, "Fatal Error: {$error['message']}", $error['file'], $error['line']);
            if (!headers_sent()) {
                http_response_code(500);
            }
        }
    }

    /**
     * Step 7: Define generic 404 page
     */
    public static function render404View(string $path, string $reason): void {
        header('Content-Type: text/html; charset=utf-8');
        include __DIR__ . '/../views/errors/404.php';
    }
}
