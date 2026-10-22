<?php

require_once __DIR__ . '/../models/ApplicationModel.php';
require_once __DIR__ . '/../models/JobPostingModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class ApiApplicationController {
    private ApplicationModel $model;
    private JobPostingModel $jobPostingModel;
    private UserModel $userModel;

    public function __construct() {
        $this->model = new ApplicationModel();
        $this->jobPostingModel = new JobPostingModel();
        $this->userModel = new UserModel();
    }

    /**
     * DIRECT ROUTE: GET /api/applications
     */
    public function index() {
        http_response_code(200);
        return $this->model->getAll();
    }

    /**
     * DIRECT ROUTE: GET /api/applications/{id}
     */
    public function show(int $id) {
        $item = $this->model->getById($id);
        if ($item) {
            http_response_code(200);
            return $item;
        }
        http_response_code(404);
        return ['error' => 'Başvuru bulunamadı.'];
    }

    /**
     * DIRECT ROUTE: POST /api/applications
     */
    public function create(array $data) {
        if (!$data || !is_array($data) || empty($data['job_posting_id']) || empty($data['applicant_name']) || empty($data['applicant_email'])) {
            http_response_code(400);
            return ['error' => 'Geçersiz veri. job_posting_id, applicant_name ve applicant_email alanları zorunludur.'];
        }

        $job = $this->jobPostingModel->getById((int)$data['job_posting_id']);
        if (!$job) {
            http_response_code(404);
            return ['error' => 'Başvurulmak istenen iş ilanı bulunamadı.'];
        }

        http_response_code(201);
        return $this->model->create($data);
    }

    /**
     * DIRECT ROUTE: PUT/PATCH /api/applications/{id}
     */
    public function update(int $id, array $data, bool $isPatch = false) {
        if (!$data || empty($data)) {
            http_response_code(400);
            return ['error' => 'Geçersiz veri.'];
        }

        $updated = $this->model->update($id, $data, $isPatch);
        if ($updated) {
            http_response_code(200);
            return $updated;
        }
        http_response_code(404);
        return ['error' => 'Başvuru bulunamadı.'];
    }

    /**
     * DIRECT ROUTE: DELETE /api/applications/{id}
     */
    public function delete(int $id) {
        if ($this->model->delete($id)) {
            http_response_code(200);
            return ['message' => 'Başvuru başarıyla silindi.'];
        }
        http_response_code(404);
        return ['error' => 'Başvuru bulunamadı.'];
    }

    /**
     * NESTED ROUTE: GET /api/job-postings/{job_id}/applications
     * List all applications for a specific job posting
     */
    public function jobPostingApplications(int $jobId) {
        $job = $this->jobPostingModel->getById($jobId);
        if (!$job) {
            http_response_code(404);
            return ['error' => 'İş ilanı bulunamadı.'];
        }

        http_response_code(200);
        return [
            'job_posting' => [
                'id' => $job['id'],
                'title' => $job['title'],
                'company' => $job['company']
            ],
            'applications' => $this->model->getByJobPostingId($jobId)
        ];
    }

    /**
     * NESTED ROUTE: POST /api/job-postings/{job_id}/applications
     * Submit an application directly nested under a job posting
     */
    public function createJobPostingApplication(int $jobId, array $data) {
        $job = $this->jobPostingModel->getById($jobId);
        if (!$job) {
            http_response_code(404);
            return ['error' => 'İş ilanı bulunamadı.'];
        }

        if (!$data || !is_array($data) || empty($data['applicant_name']) || empty($data['applicant_email'])) {
            http_response_code(400);
            return ['error' => 'Geçersiz veri. applicant_name ve applicant_email alanları zorunludur.'];
        }

        $data['job_posting_id'] = $jobId;
        http_response_code(201);
        return $this->model->create($data);
    }

    /**
     * NESTED ROUTE: GET /api/users/{user_id}/applications
     * List all applications made by a specific user
     */
    public function userApplications(int $userId) {
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            http_response_code(404);
            return ['error' => 'Kullanıcı bulunamadı.'];
        }

        http_response_code(200);
        return [
            'user' => [
                'id' => $user['id'],
                'name' => trim(($user['isim'] ?? $user['İsim'] ?? '') . ' ' . ($user['soyisim'] ?? $user['Soyisim'] ?? ''))
            ],
            'applications' => $this->model->getByUserId($userId)
        ];
    }
}
