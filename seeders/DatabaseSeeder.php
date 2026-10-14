<?php
require_once __DIR__ . '/UserSeeder.php';
require_once __DIR__ . '/AnnouncementSeeder.php';

/**
 * DatabaseSeeder
 * Master seeder to execute all seeders
 */
class DatabaseSeeder {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * Run all seeders
     */
    public function run(): void {
        echo "Seeding Users...\n";
        $userSeeder = new UserSeeder($this->pdo);
        $userSeeder->run(10);
        echo "  [OK] 10 users seeded.\n";

        echo "Seeding Announcements...\n";
        $announcementSeeder = new AnnouncementSeeder($this->pdo);
        $announcementSeeder->run(5);
        echo "  [OK] 5 announcements seeded.\n";

        echo "Database seeding completed successfully!\n";
    }

    /**
     * Generate full SQL file for seeders
     */
    public function toSql(): string {
        $userSeeder = new UserSeeder();
        $announcementSeeder = new AnnouncementSeeder();

        $sql = "-- Database Seed Script\n\n";
        $sql .= $userSeeder->toSql(10);
        $sql .= "\n";
        $sql .= $announcementSeeder->toSql(5);
        return $sql;
    }
}
