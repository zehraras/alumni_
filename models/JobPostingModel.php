<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/UserModel.php';

/**
 * JobPostingModel
 * Handles database operations for job_postings table
 * Relationship: Belongs to User (user_id -> users.id)
 */
class JobPostingModel {
    private ?PDO $db;
    private string $dataFile;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->dataFile = '/tmp/alumni_data/job_postings.json';
        if (!file_exists($this->dataFile)) {
            $dir = dirname($this->dataFile);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            file_put_contents($this->dataFile, '[]');
        }
    }

    private function mapRow(array $row): array {
        $authorName = null;
        if (!empty($row['user_isim']) || !empty($row['user_soyisim'])) {
            $authorName = trim(($row['user_isim'] ?? '') . ' ' . ($row['user_soyisim'] ?? ''));
        }

        return array_merge($row, [
            'id' => (int)$row['id'],
            'user_id' => isset($row['user_id']) && $row['user_id'] !== null ? (int)$row['user_id'] : null,
            'title' => $row['title'] ?? '',
            'company' => $row['company'] ?? '',
            'location' => $row['location'] ?? '',
            'description' => $row['description'] ?? '',
            'requirements' => $row['requirements'] ?? '',
            'salary_range' => $row['salary_range'] ?? '',
            'job_type' => $row['job_type'] ?? 'Full-time',
            'author_name' => $authorName,
            'createdAt' => $row['created_at'] ?? null,
            'updatedAt' => $row['updated_at'] ?? null,
        ]);
    }

    /**
     * READ: Get all job postings with user relationship
     */
    public function getAll(): array {
        if ($this->db) {
            try {
                $sql = "
                    SELECT j.*, u.isim as user_isim, u.soyisim as user_soyisim, u.bolum as user_bolum 
                    FROM job_postings j
                    LEFT JOIN users u ON j.user_id = u.id
                    ORDER BY j.id DESC
                ";
                $stmt = $this->db->query($sql);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return array_map([$this, 'mapRow'], $rows);
            } catch (PDOException $e) {
                error_log("DB JobPosting getAll error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $json = file_get_contents($this->dataFile);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    /**
     * READ: Get a single job posting by ID with author details
     */
    public function getById(int $id): ?array {
        if ($this->db) {
            try {
                $sql = "
                    SELECT j.*, u.isim as user_isim, u.soyisim as user_soyisim, u.bolum as user_bolum 
                    FROM job_postings j
                    LEFT JOIN users u ON j.user_id = u.id
                    WHERE j.id = :id
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $this->mapRow($row) : null;
            } catch (PDOException $e) {
                error_log("DB JobPosting getById error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $items = $this->getAll();
        foreach ($items as $item) {
            if (isset($item['id']) && (int)$item['id'] === $id) return $item;
        }
        return null;
    }

    /**
     * RELATIONSHIP: Get all job postings created by a specific user
     */
    public function getByUserId(int $userId): array {
        if ($this->db) {
            try {
                $sql = "
                    SELECT j.*, u.isim as user_isim, u.soyisim as user_soyisim, u.bolum as user_bolum 
                    FROM job_postings j
                    LEFT JOIN users u ON j.user_id = u.id
                    WHERE j.user_id = :user_id
                    ORDER BY j.id DESC
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':user_id' => $userId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return array_map([$this, 'mapRow'], $rows);
            } catch (PDOException $e) {
                error_log("DB JobPosting getByUserId error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $items = $this->getAll();
        return array_values(array_filter($items, fn($i) => isset($i['user_id']) && (int)$i['user_id'] === $userId));
    }

    /**
     * RELATIONSHIP: Get the User model instance of the job author
     */
    public function getUser(int $jobPostingId): ?array {
        $posting = $this->getById($jobPostingId);
        if (!$posting || empty($posting['user_id'])) {
            return null;
        }
        $userModel = new UserModel();
        return $userModel->getUserById((int)$posting['user_id']);
    }

    /**
     * CREATE: Add a new job posting
     */
    public function create(array $data): array {
        $userId = isset($data['user_id']) && $data['user_id'] !== '' ? (int)$data['user_id'] : null;
        $title = $data['title'] ?? '';
        $company = $data['company'] ?? '';
        $location = $data['location'] ?? '';
        $description = $data['description'] ?? '';
        $requirements = $data['requirements'] ?? '';
        $salaryRange = $data['salary_range'] ?? $data['salary'] ?? '';
        $jobType = $data['job_type'] ?? $data['type'] ?? 'Full-time';

        if ($this->db) {
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO job_postings (user_id, title, company, location, description, requirements, salary_range, job_type, created_at, updated_at)
                    VALUES (:user_id, :title, :company, :location, :description, :requirements, :salary_range, :job_type, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    RETURNING *
                ");
                $stmt->execute([
                    ':user_id' => $userId,
                    ':title' => $title,
                    ':company' => $company,
                    ':location' => $location,
                    ':description' => $description,
                    ':requirements' => $requirements,
                    ':salary_range' => $salaryRange,
                    ':job_type' => $jobType
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) return $this->mapRow($row);
            } catch (PDOException $e) {
                error_log("DB JobPosting create error: " . $e->getMessage());
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

    /**
     * UPDATE: Update an existing job posting
     */
    public function update(int $id, array $updateData, bool $isPatch = false): ?array {
        if ($this->db) {
            try {
                $existing = $this->getById($id);
                if (!$existing) return null;

                $data = $isPatch ? array_merge($existing, $updateData) : $updateData;

                $userId = isset($data['user_id']) && $data['user_id'] !== '' ? (int)$data['user_id'] : $existing['user_id'];
                $title = $data['title'] ?? $existing['title'];
                $company = $data['company'] ?? $existing['company'];
                $location = $data['location'] ?? $existing['location'];
                $description = $data['description'] ?? $existing['description'];
                $requirements = $data['requirements'] ?? $existing['requirements'];
                $salaryRange = $data['salary_range'] ?? $existing['salary_range'];
                $jobType = $data['job_type'] ?? $existing['job_type'];

                $stmt = $this->db->prepare("
                    UPDATE job_postings 
                    SET user_id = :user_id, title = :title, company = :company, location = :location,
                        description = :description, requirements = :requirements, salary_range = :salary_range,
                        job_type = :job_type, updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                    RETURNING *
                ");
                $stmt->execute([
                    ':user_id' => $userId,
                    ':title' => $title,
                    ':company' => $company,
                    ':location' => $location,
                    ':description' => $description,
                    ':requirements' => $requirements,
                    ':salary_range' => $salaryRange,
                    ':job_type' => $jobType,
                    ':id' => $id
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $this->mapRow($row) : null;
            } catch (PDOException $e) {
                error_log("DB JobPosting update error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $items = $this->getAll();
        foreach ($items as $key => $item) {
            if (isset($item['id']) && (int)$item['id'] === $id) {
                $items[$key] = $isPatch ? array_merge($item, $updateData) : array_merge(['id' => $id], $updateData);
                $items[$key]['updatedAt'] = date('Y-m-d\TH:i:s\Z');
                $this->save($items);
                return $items[$key];
            }
        }
        return null;
    }

    /**
     * DELETE: Delete a job posting
     */
    public function delete(int $id): bool {
        if ($this->db) {
            try {
                $stmt = $this->db->prepare("DELETE FROM job_postings WHERE id = :id");
                $stmt->execute([':id' => $id]);
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("DB JobPosting delete error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $items = $this->getAll();
        $initialCount = count($items);
        $items = array_filter($items, fn($i) => isset($i['id']) && (int)$i['id'] !== $id);
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
