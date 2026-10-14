<?php
/**
 * Database Connection Manager (Singleton)
 * Connects to PostgreSQL using PDO
 */
class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): ?PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Load .env variables if .env exists
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) continue;
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v);
                    if (!getenv($k)) {
                        putenv("$k=$v");
                        $_ENV[$k] = $v;
                    }
                }
            }
        }

        // Host 'db' inside docker network, or 'localhost'/'127.0.0.1' on host
        $host = getenv('DB_HOST') ?: 'db';
        $port = getenv('DB_PORT') ?: '5432';
        $dbname = getenv('POSTGRES_DB') ?: (getenv('DB_NAME') ?: 'alumni_db');
        $user = getenv('POSTGRES_USER') ?: (getenv('DB_USER') ?: 'alumni_user');
        $pass = getenv('POSTGRES_PASSWORD') ?: (getenv('DB_PASSWORD') ?: 'alumni_password');

        if (!extension_loaded('pdo_pgsql')) {
            return null;
        }

        try {
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 3
            ]);
            return self::$instance;
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            return null;
        }
    }
}
