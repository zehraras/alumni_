<?php
require_once __DIR__ . '/../factories/JobPostingFactory.php';

/**
 * JobPostingSeeder
 * Seeds job_postings table with sample records
 */
class JobPostingSeeder {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    public function run(int $count = 8): array {
        $postings = JobPostingFactory::count($count);

        if ($this->pdo) {
            $stmt = $this->pdo->prepare("
                INSERT INTO job_postings (user_id, title, company, location, description, requirements, salary_range, job_type, created_at, updated_at)
                VALUES (:user_id, :title, :company, :location, :description, :requirements, :salary_range, :job_type, :created_at, :updated_at)
            ");

            foreach ($postings as $job) {
                $stmt->execute([
                    ':user_id' => $job['user_id'],
                    ':title' => $job['title'],
                    ':company' => $job['company'],
                    ':location' => $job['location'],
                    ':description' => $job['description'],
                    ':requirements' => $job['requirements'],
                    ':salary_range' => $job['salary_range'],
                    ':job_type' => $job['job_type'],
                    ':created_at' => $job['created_at'],
                    ':updated_at' => $job['updated_at']
                ]);
            }
        }

        return $postings;
    }

    public function toSql(int $count = 8): string {
        $postings = JobPostingFactory::count($count);
        $sql = "-- Seed job_postings table\n";
        foreach ($postings as $j) {
            $sql .= sprintf(
                "INSERT INTO job_postings (user_id, title, company, location, description, requirements, salary_range, job_type, created_at, updated_at) VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');\n",
                $j['user_id'],
                addslashes($j['title']),
                addslashes($j['company']),
                addslashes($j['location']),
                addslashes($j['description']),
                addslashes($j['requirements']),
                addslashes($j['salary_range']),
                addslashes($j['job_type']),
                $j['created_at'],
                $j['updated_at']
            );
        }
        return $sql;
    }
}
