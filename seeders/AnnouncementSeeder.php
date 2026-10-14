<?php
require_once __DIR__ . '/../factories/AnnouncementFactory.php';

/**
 * AnnouncementSeeder
 * Seeds announcements table with sample records
 */
class AnnouncementSeeder {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * Run the announcement seeder
     */
    public function run(int $count = 5): array {
        $announcements = AnnouncementFactory::count($count);

        if ($this->pdo) {
            $stmt = $this->pdo->prepare("
                INSERT INTO announcements (title, content, created_at, updated_at)
                VALUES (:title, :content, :created_at, :updated_at)
            ");

            foreach ($announcements as $item) {
                $stmt->execute([
                    ':title' => $item['title'],
                    ':content' => $item['content'],
                    ':created_at' => $item['created_at'],
                    ':updated_at' => $item['updated_at']
                ]);
            }
        }

        return $announcements;
    }

    /**
     * Generate SQL INSERT queries
     */
    public function toSql(int $count = 5): string {
        $announcements = AnnouncementFactory::count($count);
        $sql = "-- Seed announcements table\n";
        foreach ($announcements as $a) {
            $sql .= sprintf(
                "INSERT INTO announcements (title, content, created_at, updated_at) VALUES ('%s', '%s', '%s', '%s');\n",
                addslashes($a['title']),
                addslashes($a['content']),
                $a['created_at'],
                $a['updated_at']
            );
        }
        return $sql;
    }
}
