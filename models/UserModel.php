<?php
require_once __DIR__ . '/Database.php';

class UserModel {
    private ?PDO $db;
    private string $dataFile;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->dataFile = '/tmp/alumni_data/users.json';
        if (!file_exists($this->dataFile)) {
            $dir = dirname($this->dataFile);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            file_put_contents($this->dataFile, '[]');
        }
    }

    /**
     * Map database row to standard user array
     */
    private function mapRow(array $row): array {
        return array_merge($row, [
            'id' => (int)$row['id'],
            'İsim' => $row['isim'] ?? '',
            'Soyisim' => $row['soyisim'] ?? '',
            'Bölüm' => $row['bolum'] ?? '',
            'MezuniyetYılı' => $row['mezuniyet_yili'] ?? null,
            'Şehir' => $row['sehir'] ?? '',
            'Pozisyon' => $row['pozisyon'] ?? '',
            'createdAt' => $row['created_at'] ?? null,
            'updatedAt' => $row['updated_at'] ?? null,
        ]);
    }

    /**
     * READ: Get all users
     */
    public function getAllUsers(): array {
        if ($this->db) {
            try {
                $stmt = $this->db->query("SELECT * FROM users ORDER BY id ASC");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return array_map([$this, 'mapRow'], $rows);
            } catch (PDOException $e) {
                error_log("DB getAllUsers error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        if (!file_exists($this->dataFile)) return [];
        $json = file_get_contents($this->dataFile);
        $users = json_decode($json, true);
        return is_array($users) ? $users : [];
    }

    /**
     * READ: Get a single user by ID
     */
    public function getUserById(int $id): ?array {
        if ($this->db) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $this->mapRow($row) : null;
            } catch (PDOException $e) {
                error_log("DB getUserById error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if (isset($user['id']) && (int)$user['id'] === $id) {
                return $user;
            }
        }
        return null;
    }

    /**
     * CREATE: Add a new user
     */
    public function createUser(array $userData): array {
        $isim = $userData['isim'] ?? $userData['İsim'] ?? $userData['name'] ?? '';
        $soyisim = $userData['soyisim'] ?? $userData['Soyisim'] ?? $userData['surname'] ?? '';
        $bolum = $userData['bolum'] ?? $userData['Bölüm'] ?? $userData['department'] ?? '';
        $yil = $userData['mezuniyet_yili'] ?? $userData['MezuniyetYılı'] ?? $userData['graduationYear'] ?? null;
        $yil = $yil ? (int)$yil : null;
        $sehir = $userData['sehir'] ?? $userData['Şehir'] ?? $userData['city'] ?? '';
        $pozisyon = $userData['pozisyon'] ?? $userData['Pozisyon'] ?? $userData['position'] ?? '';

        if ($this->db) {
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO users (isim, soyisim, bolum, mezuniyet_yili, sehir, pozisyon, created_at, updated_at)
                    VALUES (:isim, :soyisim, :bolum, :mezuniyet_yili, :sehir, :pozisyon, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    RETURNING *
                ");
                $stmt->execute([
                    ':isim' => $isim,
                    ':soyisim' => $soyisim,
                    ':bolum' => $bolum,
                    ':mezuniyet_yili' => $yil,
                    ':sehir' => $sehir,
                    ':pozisyon' => $pozisyon
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) return $this->mapRow($row);
            } catch (PDOException $e) {
                error_log("DB createUser error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $users = $this->getAllUsers();
        $maxId = 0;
        foreach ($users as $u) {
            if (isset($u['id']) && $u['id'] > $maxId) $maxId = $u['id'];
        }
        $newUser = array_merge(['id' => $maxId + 1, 'createdAt' => date('c')], $userData);
        $users[] = $newUser;
        $this->saveUsers($users);
        return $newUser;
    }

    /**
     * UPDATE: Update an existing user
     */
    public function updateUser(int $id, array $updateData, bool $isPatch = false): ?array {
        if ($this->db) {
            try {
                $existing = $this->getUserById($id);
                if (!$existing) return null;

                $data = $isPatch ? array_merge($existing, $updateData) : $updateData;

                $isim = $data['isim'] ?? $data['İsim'] ?? $data['name'] ?? $existing['isim'];
                $soyisim = $data['soyisim'] ?? $data['Soyisim'] ?? $data['surname'] ?? $existing['soyisim'];
                $bolum = $data['bolum'] ?? $data['Bölüm'] ?? $data['department'] ?? $existing['bolum'];
                $yil = $data['mezuniyet_yili'] ?? $data['MezuniyetYılı'] ?? $data['graduationYear'] ?? $existing['mezuniyet_yili'];
                $yil = $yil ? (int)$yil : null;
                $sehir = $data['sehir'] ?? $data['Şehir'] ?? $data['city'] ?? $existing['sehir'];
                $pozisyon = $data['pozisyon'] ?? $data['Pozisyon'] ?? $data['position'] ?? $existing['pozisyon'];

                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET isim = :isim, soyisim = :soyisim, bolum = :bolum, mezuniyet_yili = :mezuniyet_yili, 
                        sehir = :sehir, pozisyon = :pozisyon, updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                    RETURNING *
                ");
                $stmt->execute([
                    ':isim' => $isim,
                    ':soyisim' => $soyisim,
                    ':bolum' => $bolum,
                    ':mezuniyet_yili' => $yil,
                    ':sehir' => $sehir,
                    ':pozisyon' => $pozisyon,
                    ':id' => $id
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $this->mapRow($row) : null;
            } catch (PDOException $e) {
                error_log("DB updateUser error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $users = $this->getAllUsers();
        foreach ($users as $key => $user) {
            if (isset($user['id']) && (int)$user['id'] === $id) {
                $users[$key] = $isPatch ? array_merge($user, $updateData) : array_merge(['id' => $id], $updateData);
                $users[$key]['updatedAt'] = date('Y-m-d\TH:i:s\Z');
                $this->saveUsers($users);
                return $users[$key];
            }
        }
        return null;
    }

    /**
     * DELETE: Remove a user by ID
     */
    public function deleteUser(int $id): bool {
        if ($this->db) {
            try {
                $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
                $stmt->execute([':id' => $id]);
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("DB deleteUser error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $users = $this->getAllUsers();
        $initialCount = count($users);
        $users = array_filter($users, fn($u) => isset($u['id']) && (int)$u['id'] !== $id);
        if (count($users) < $initialCount) {
            $this->saveUsers(array_values($users));
            return true;
        }
        return false;
    }

    /**
     * RELATIONSHIP: Get all job postings posted by this user
     */
    public function getJobPostings(int $userId): array {
        if ($this->db) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM job_postings WHERE user_id = :user_id ORDER BY id DESC");
                $stmt->execute([':user_id' => $userId]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("DB UserModel getJobPostings error: " . $e->getMessage());
            }
        }
        return [];
    }

    private function saveUsers(array $users): void {
        file_put_contents($this->dataFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
