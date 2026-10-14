<?php
/**
 * Database Migration Script
 * 
 * Usage:
 *   php migrate.php
 * Or via Docker:
 *   docker compose exec db psql -U alumni_user -d alumni_db -f /var/www/html/migrations/...
 */

// Load .env variables if .env exists
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            putenv("$key=$val");
            $_ENV[$key] = $val;
        }
    }
}

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('POSTGRES_DB') ?: (getenv('DB_NAME') ?: 'alumni_db');
$user = getenv('POSTGRES_USER') ?: (getenv('DB_USER') ?: 'alumni_user');
$pass = getenv('POSTGRES_PASSWORD') ?: (getenv('DB_PASSWORD') ?: 'alumni_password');

echo "=== Running Database Migrations ===\n";
echo "Target Database: $dbname@$host:$port (User: $user)\n\n";

$migrationsDir = __DIR__ . '/migrations';
if (!is_dir($migrationsDir)) {
    echo "Error: migrations directory not found at $migrationsDir\n";
    exit(1);
}

$files = glob($migrationsDir . '/*.sql');
sort($files);

if (empty($files)) {
    echo "No migration files found.\n";
    exit(0);
}

if (extension_loaded('pdo_pgsql')) {
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        foreach ($files as $file) {
            $filename = basename($file);
            echo "Running migration: $filename ... ";
            $sql = file_get_contents($file);
            $pdo->exec($sql);
            echo "[OK]\n";
        }

        echo "\nAll migrations completed successfully!\n";
    } catch (PDOException $e) {
        echo "[ERROR] Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    // If pdo_pgsql is not loaded in PHP, execute via shell psql or docker
    echo "Note: PHP pdo_pgsql extension not loaded. Attempting fallback execution...\n";
    $success = true;
    foreach ($files as $file) {
        $filename = basename($file);
        echo "Running migration: $filename ... ";
        $cmd = sprintf(
            'PGPASSWORD=%s psql -h %s -p %s -U %s -d %s -f %s 2>&1',
            escapeshellarg($pass),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($dbname),
            escapeshellarg($file)
        );
        exec($cmd, $out, $ret);
        if ($ret === 0) {
            echo "[OK]\n";
        } else {
            echo "[ERROR]\n";
            echo implode("\n", $out) . "\n";
            $success = false;
            break;
        }
    }
    if ($success) {
        echo "\nAll migrations completed successfully!\n";
    }
}
