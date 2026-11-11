<?php

require_once __DIR__ . '/../models/JobPostingModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class ApiJobPostingController {
    private JobPostingModel $model;
    private UserModel $userModel;

    public function __construct() {
        $this->model = new JobPostingModel();
        $this->userModel = new UserModel();
    }

    /**
     * DIRECT ROUTE: GET /api/job-postings
     */
    public function index() {
        http_response_code(200);
        return $this->model->getAll();
    }

    /**
     * DIRECT ROUTE: GET /api/job-postings/{id}
     */
    public function show(int $id) {
        $item = $this->model->getById($id);
        if ($item) {
            http_response_code(200);
            return $item;
        }
        http_response_code(404);
        return ['error' => 'İş ilanı bulunamadı.'];
    }

    /**
     * DIRECT ROUTE: POST /api/job-postings
     */
    public function create(array $data) {
        $errors = [];

        if (empty($data['title']) || trim($data['title']) === '') {
            $errors['title'] = 'Title is required.';
        } elseif (mb_strlen(trim($data['title'])) > 120) {
            $errors['title'] = 'Title cannot exceed 120 characters.';
        }

        if (empty($data['company']) || trim($data['company']) === '') {
            $errors['company'] = 'Company is required.';
        }

        if (!empty($data['user_id'])) {
            $user = $this->userModel->getUserById((int)$data['user_id']);
            if (!$user) {
                $errors['user_id'] = 'Specified user (user_id) does not exist.';
            }
        }

        if (!empty($errors)) {
            http_response_code(422);
            return [
                'error' => 'Validation Failed',
                'reason' => 'Request body contains invalid or missing required fields.',
                'errors' => $errors
            ];
        }

        http_response_code(201);
        return $this->model->create($data);
    }

    /**
     * DIRECT ROUTE: PUT/PATCH /api/job-postings/{id}
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
        return ['error' => 'İş ilanı bulunamadı.'];
    }

    /**
     * DIRECT ROUTE: DELETE /api/job-postings/{id}
     */
    public function delete(int $id) {
        if ($this->model->delete($id)) {
            http_response_code(200);
            return ['message' => 'İş ilanı başarıyla silindi.'];
        }
        http_response_code(404);
        return ['error' => 'İş ilanı bulunamadı.'];
    }

    /**
     * NESTED ROUTE: GET /api/users/{user_id}/job-postings
     * Returns all job postings belonging to a specific user
     */
    public function userJobPostings(int $userId) {
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
            'job_postings' => $this->model->getByUserId($userId)
        ];
    }

    /**
     * NESTED ROUTE: POST /api/users/{user_id}/job-postings
     * Creates a job posting directly assigned to a specific user
     */
    public function createUserJobPosting(int $userId, array $data) {
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            http_response_code(404);
            return [
                'error' => 'Not Found',
                'reason' => "User with ID $userId does not exist."
            ];
        }

        $errors = [];
        if (empty($data['title']) || trim($data['title']) === '') {
            $errors['title'] = 'Title is required.';
        } elseif (mb_strlen(trim($data['title'])) > 120) {
            $errors['title'] = 'Title cannot exceed 120 characters.';
        }

        if (empty($data['company']) || trim($data['company']) === '') {
            $errors['company'] = 'Company is required.';
        }

        if (!empty($errors)) {
            http_response_code(422);
            return [
                'error' => 'Validation Failed',
                'reason' => 'Request body contains invalid or missing required fields.',
                'errors' => $errors
            ];
        }

        $data['user_id'] = $userId;
        http_response_code(201);
        return $this->model->create($data);
    }
}
