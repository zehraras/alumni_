<?php

require_once __DIR__ . '/../models/AnnouncementModel.php';

class ApiAnnouncementController {
    private AnnouncementModel $model;

    public function __construct() {
        $this->model = new AnnouncementModel();
    }

    public function create(array $data) {
        if (!$data || !is_array($data) || empty($data)) {
            http_response_code(400);
            return ['error' => 'Geçersiz veri.'];
        }
        http_response_code(201);
        return $this->model->create($data);
    }

    public function index() {
        http_response_code(200);
        return $this->model->getAll();
    }

    public function show(int $id) {
        $item = $this->model->getById($id);
        if ($item) {
            http_response_code(200);
            return $item;
        }
        http_response_code(404);
        return ['error' => 'Duyuru bulunamadı.'];
    }

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
        return ['error' => 'Duyuru bulunamadı.'];
    }

    public function delete(int $id) {
        if ($this->model->delete($id)) {
            http_response_code(200);
            return ['message' => 'Silindi.'];
        }
        http_response_code(404);
        return ['error' => 'Duyuru bulunamadı.'];
    }
}
