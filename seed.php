<?php
/**
 * Database Seeder Runner Script
 * 
 * Usage:
 *   php seed.php
 * Or via Docker:
 *   ./seed.sh
 */

require_once __DIR__ . '/seeders/DatabaseSeeder.php';

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

echo "=== Seeding Database ===\n";
echo "Target Database: $dbname@$host:$port (User: $user)\n\n";

if (extension_loaded('pdo_pgsql')) {
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $seeder = new DatabaseSeeder($pdo);
        $seeder->run();
    } catch (PDOException $e) {
        echo "[ERROR] Seeding failed: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "Note: PHP pdo_pgsql extension not loaded in current CLI environment.\n";
    echo "Generating seed SQL and applying via Docker/psql...\n";
    $seeder = new DatabaseSeeder();
    $sql = $seeder->toSql();
    $tmpSql = __DIR__ . '/seeders/seed.sql';
    file_put_contents($tmpSql, $sql);

    $cmd = sprintf(
        'docker exec -i alumni_postgres psql -U %s -d %s < %s 2>&1',
        escapeshellarg($user),
        escapeshellarg($dbname),
        escapeshellarg($tmpSql)
    );
    exec($cmd, $out, $ret);
    if ($ret === 0) {
        echo implode("\n", $out) . "\n";
        echo "Database seeding completed successfully via Docker!\n";
    } else {
        echo "[ERROR] Docker execution failed: " . implode("\n", $out) . "\n";
        echo "You can manually execute: ./seed.sh\n";
    }
}
