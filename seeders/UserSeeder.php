<?php
require_once __DIR__ . '/../factories/UserFactory.php';

/**
 * UserSeeder
 * Seeds users table with sample records
 */
class UserSeeder {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * Run the user seeder
     */
    public function run(int $count = 10): array {
        $users = UserFactory::count($count);

        if ($this->pdo) {
            $stmt = $this->pdo->prepare("
                INSERT INTO users (isim, soyisim, bolum, mezuniyet_yili, sehir, pozisyon, created_at, updated_at)
                VALUES (:isim, :soyisim, :bolum, :mezuniyet_yili, :sehir, :pozisyon, :created_at, :updated_at)
            ");

            foreach ($users as $user) {
                $stmt->execute([
                    ':isim' => $user['isim'],
                    ':soyisim' => $user['soyisim'],
                    ':bolum' => $user['bolum'],
                    ':mezuniyet_yili' => $user['mezuniyet_yili'],
                    ':sehir' => $user['sehir'],
                    ':pozisyon' => $user['pozisyon'],
                    ':created_at' => $user['created_at'],
                    ':updated_at' => $user['updated_at']
                ]);
            }
        }

        return $users;
    }

    /**
     * Generate SQL INSERT queries
     */
    public function toSql(int $count = 10): string {
        $users = UserFactory::count($count);
        $sql = "-- Seed users table\n";
        foreach ($users as $u) {
            $sql .= sprintf(
                "INSERT INTO users (isim, soyisim, bolum, mezuniyet_yili, sehir, pozisyon, created_at, updated_at) VALUES ('%s', '%s', '%s', %d, '%s', '%s', '%s', '%s');\n",
                addslashes($u['isim']),
                addslashes($u['soyisim']),
                addslashes($u['bolum']),
                $u['mezuniyet_yili'],
                addslashes($u['sehir']),
                addslashes($u['pozisyon']),
                $u['created_at'],
                $u['updated_at']
            );
        }
        return $sql;
    }
}
