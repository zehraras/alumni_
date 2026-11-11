<?php
/**
 * LoggerMiddleware
 * 
 * Writes request access log to the terminal (STDOUT / stderr).
 * Format: time method route res.type duration
 * Example: 10:20:30 POST /user 201 18ms
 */
class LoggerMiddleware {
    private float $startTime;
    private string $method;
    private string $route;

    public function __construct() {
        $this->startTime = microtime(true);
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->route = $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * Call at the end of the request (or register shutdown)
     */
    public function log(int $statusCode): void {
        $durationMs = round((microtime(true) - $this->startTime) * 1000);
        $time = date('H:i:s');
        
        // Format as written on the board:
        // time method route res.type duration
        // 10:20:30 POST /user 201 18ms
        $logLine = sprintf(
            "[%s] %-6s %-30s %d %dms\n",
            $time,
            $this->method,
            $this->route,
            $statusCode,
            $durationMs
        );

        // 1. Output to PHP built-in server terminal / STDOUT
        file_put_contents('php://stdout', $logLine);
        // Also error_log to ensure docker logs picks it up
        error_log(trim($logLine));
    }
}
