<?php
require_once __DIR__ . '/Database.php';

class AnnouncementModel {
    private ?PDO $db;
    private string $dataFile;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->dataFile = '/tmp/alumni_data/announcements.json';
        if (!file_exists($this->dataFile)) {
            $dir = dirname($this->dataFile);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            file_put_contents($this->dataFile, '[]');
        }
    }

    private function mapRow(array $row): array {
        return array_merge($row, [
            'id' => (int)$row['id'],
            'title' => $row['title'] ?? '',
            'content' => $row['content'] ?? '',
            'createdAt' => $row['created_at'] ?? null,
            'updatedAt' => $row['updated_at'] ?? null,
        ]);
    }

    public function getAll(): array {
        if ($this->db) {
            try {
                $stmt = $this->db->query("SELECT * FROM announcements ORDER BY id DESC");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return array_map([$this, 'mapRow'], $rows);
            } catch (PDOException $e) {
                error_log("DB Announcement getAll error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $json = file_get_contents($this->dataFile);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    public function getById(int $id): ?array {
        if ($this->db) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM announcements WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $this->mapRow($row) : null;
            } catch (PDOException $e) {
                error_log("DB Announcement getById error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $items = $this->getAll();
        foreach ($items as $item) {
            if (isset($item['id']) && (int)$item['id'] === $id) return $item;
        }
        return null;
    }

    public function create(array $data): array {
        $title = $data['title'] ?? $data['başlık'] ?? '';
        $content = $data['content'] ?? $data['içerik'] ?? '';

        if ($this->db) {
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO announcements (title, content, created_at, updated_at)
                    VALUES (:title, :content, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    RETURNING *
                ");
                $stmt->execute([
                    ':title' => $title,
                    ':content' => $content
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) return $this->mapRow($row);
            } catch (PDOException $e) {
                error_log("DB Announcement create error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $items = $this->getAll();
        $maxId = 0;
        foreach ($items as $u) {
            if (isset($u['id']) && (int)$u['id'] > $maxId) $maxId = (int)$u['id'];
        }
        $newItem = array_merge([
            'id' => $maxId + 1,
            'createdAt' => date('Y-m-d\TH:i:s\Z')
        ], $data);
        array_unshift($items, $newItem);
        $this->save($items);
        return $newItem;
    }

    public function update(int $id, array $updateData, bool $isPatch = false): ?array {
        if ($this->db) {
            try {
                $existing = $this->getById($id);
                if (!$existing) return null;

                $data = $isPatch ? array_merge($existing, $updateData) : $updateData;
                $title = $data['title'] ?? $data['başlık'] ?? $existing['title'];
                $content = $data['content'] ?? $data['içerik'] ?? $existing['content'];

                $stmt = $this->db->prepare("
                    UPDATE announcements 
                    SET title = :title, content = :content, updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                    RETURNING *
                ");
                $stmt->execute([
                    ':title' => $title,
                    ':content' => $content,
                    ':id' => $id
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $this->mapRow($row) : null;
            } catch (PDOException $e) {
                error_log("DB Announcement update error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $items = $this->getAll();
        foreach ($items as $key => $item) {
            if (isset($item['id']) && (int)$item['id'] === $id) {
                $items[$key] = $isPatch ? array_merge($item, $updateData) : array_merge(['id' => $id, 'createdAt' => $item['createdAt'] ?? date('c')], $updateData);
                $items[$key]['updatedAt'] = date('Y-m-d\TH:i:s\Z');
                $this->save($items);
                return $items[$key];
            }
        }
        return null;
    }

    public function delete(int $id): bool {
        if ($this->db) {
            try {
                $stmt = $this->db->prepare("DELETE FROM announcements WHERE id = :id");
                $stmt->execute([':id' => $id]);
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("DB Announcement delete error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $items = $this->getAll();
        $initialCount = count($items);
        $items = array_filter($items, fn($item) => isset($item['id']) && (int)$item['id'] !== $id);
        if (count($items) < $initialCount) {
            $this->save(array_values($items));
            return true;
        }
        return false;
    }

    private function save(array $items): void {
        file_put_contents($this->dataFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
