<?php
require_once __DIR__ . '/Database.php';

/**
 * ApplicationModel
 * Handles database operations for applications table
 * Relationships: 
 *   - Belongs to JobPosting (job_posting_id -> job_postings.id)
 *   - Belongs to User (user_id -> users.id, optional)
 */
class ApplicationModel {
    private ?PDO $db;
    private string $dataFile;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->dataFile = '/tmp/alumni_data/applications.json';
        if (!file_exists($this->dataFile)) {
            $dir = dirname($this->dataFile);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            file_put_contents($this->dataFile, '[]');
        }
    }

    private function mapRow(array $row): array {
        return array_merge($row, [
            'id' => (int)$row['id'],
            'job_posting_id' => (int)$row['job_posting_id'],
            'user_id' => isset($row['user_id']) && $row['user_id'] !== null ? (int)$row['user_id'] : null,
            'applicant_name' => $row['applicant_name'] ?? '',
            'applicant_email' => $row['applicant_email'] ?? '',
            'resume_url' => $row['resume_url'] ?? '',
            'cover_letter' => $row['cover_letter'] ?? '',
            'status' => $row['status'] ?? 'Pending',
            'job_title' => $row['job_title'] ?? null,
            'job_company' => $row['job_company'] ?? null,
            'createdAt' => $row['created_at'] ?? null,
            'updatedAt' => $row['updated_at'] ?? null,
        ]);
    }

    /**
     * READ: Get all applications with job posting details
     */
    public function getAll(): array {
        if ($this->db) {
            try {
                $sql = "
                    SELECT a.*, j.title as job_title, j.company as job_company
                    FROM applications a
                    LEFT JOIN job_postings j ON a.job_posting_id = j.id
                    ORDER BY a.id DESC
                ";
                $stmt = $this->db->query($sql);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return array_map([$this, 'mapRow'], $rows);
            } catch (PDOException $e) {
                error_log("DB Application getAll error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $json = file_get_contents($this->dataFile);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    /**
     * READ: Get a single application by ID
     */
    public function getById(int $id): ?array {
        if ($this->db) {
            try {
                $sql = "
                    SELECT a.*, j.title as job_title, j.company as job_company
                    FROM applications a
                    LEFT JOIN job_postings j ON a.job_posting_id = j.id
                    WHERE a.id = :id
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $this->mapRow($row) : null;
            } catch (PDOException $e) {
                error_log("DB Application getById error: " . $e->getMessage());
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
     * RELATIONSHIP: Get all applications submitted for a specific job posting
     */
    public function getByJobPostingId(int $jobPostingId): array {
        if ($this->db) {
            try {
                $sql = "
                    SELECT a.*, j.title as job_title, j.company as job_company
                    FROM applications a
                    LEFT JOIN job_postings j ON a.job_posting_id = j.id
                    WHERE a.job_posting_id = :job_posting_id
                    ORDER BY a.id DESC
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':job_posting_id' => $jobPostingId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return array_map([$this, 'mapRow'], $rows);
            } catch (PDOException $e) {
                error_log("DB Application getByJobPostingId error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $items = $this->getAll();
        return array_values(array_filter($items, fn($i) => isset($i['job_posting_id']) && (int)$i['job_posting_id'] === $jobPostingId));
    }

    /**
     * RELATIONSHIP: Get all applications submitted by a specific user
     */
    public function getByUserId(int $userId): array {
        if ($this->db) {
            try {
                $sql = "
                    SELECT a.*, j.title as job_title, j.company as job_company
                    FROM applications a
                    LEFT JOIN job_postings j ON a.job_posting_id = j.id
                    WHERE a.user_id = :user_id
                    ORDER BY a.id DESC
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':user_id' => $userId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return array_map([$this, 'mapRow'], $rows);
            } catch (PDOException $e) {
                error_log("DB Application getByUserId error: " . $e->getMessage());
            }
        }

        // Fallback to JSON
        $items = $this->getAll();
        return array_values(array_filter($items, fn($i) => isset($i['user_id']) && (int)$i['user_id'] === $userId));
    }

    /**
     * CREATE: Submit a new application
     */
    public function create(array $data): array {
        $jobPostingId = (int)($data['job_posting_id'] ?? 0);
        $userId = isset($data['user_id']) && $data['user_id'] !== '' ? (int)$data['user_id'] : null;
        $name = $data['applicant_name'] ?? $data['name'] ?? '';
        $email = $data['applicant_email'] ?? $data['email'] ?? '';
        $resumeUrl = $data['resume_url'] ?? $data['cv'] ?? '';
        $coverLetter = $data['cover_letter'] ?? '';
        $status = $data['status'] ?? 'Pending';

        if ($this->db) {
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO applications (job_posting_id, user_id, applicant_name, applicant_email, resume_url, cover_letter, status, created_at, updated_at)
                    VALUES (:job_posting_id, :user_id, :applicant_name, :applicant_email, :resume_url, :cover_letter, :status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    RETURNING *
                ");
                $stmt->execute([
                    ':job_posting_id' => $jobPostingId,
                    ':user_id' => $userId,
                    ':applicant_name' => $name,
                    ':applicant_email' => $email,
                    ':resume_url' => $resumeUrl,
                    ':cover_letter' => $coverLetter,
                    ':status' => $status
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) return $this->mapRow($row);
            } catch (PDOException $e) {
                error_log("DB Application create error: " . $e->getMessage());
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
     * UPDATE: Update application status or fields
     */
    public function update(int $id, array $updateData, bool $isPatch = false): ?array {
        if ($this->db) {
            try {
                $existing = $this->getById($id);
                if (!$existing) return null;

                $data = $isPatch ? array_merge($existing, $updateData) : $updateData;

                $status = $data['status'] ?? $existing['status'];
                $coverLetter = $data['cover_letter'] ?? $existing['cover_letter'];
                $resumeUrl = $data['resume_url'] ?? $existing['resume_url'];

                $stmt = $this->db->prepare("
                    UPDATE applications 
                    SET status = :status, cover_letter = :cover_letter, resume_url = :resume_url, updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                    RETURNING *
                ");
                $stmt->execute([
                    ':status' => $status,
                    ':cover_letter' => $coverLetter,
                    ':resume_url' => $resumeUrl,
                    ':id' => $id
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $this->mapRow($row) : null;
            } catch (PDOException $e) {
                error_log("DB Application update error: " . $e->getMessage());
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
     * DELETE: Delete an application
     */
    public function delete(int $id): bool {
        if ($this->db) {
            try {
                $stmt = $this->db->prepare("DELETE FROM applications WHERE id = :id");
                $stmt->execute([':id' => $id]);
                return $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("DB Application delete error: " . $e->getMessage());
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
