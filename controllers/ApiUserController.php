<?php

require_once __DIR__ . '/../models/UserModel.php';

class ApiUserController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // CREATE
    public function create(array $data) {
        if (!$data || !is_array($data) || empty($data)) {
            http_response_code(400);
            return ['error' => 'Geçersiz veya boş veri.'];
        }

        $newUser = $this->userModel->createUser($data);
        http_response_code(201);
        return $newUser;
    }

    // READ ALL
    public function index() {
        http_response_code(200);
        return $this->userModel->getAllUsers();
    }

    // READ SINGLE
    public function show(int $id) {
        $user = $this->userModel->getUserById($id);
        if ($user) {
            http_response_code(200);
            return $user;
        } else {
            http_response_code(404);
            return ['error' => 'Kullanıcı bulunamadı.'];
        }
    }

    // UPDATE
    public function update(int $id, array $data, bool $isPatch = false) {
        if (!$data || !is_array($data) || empty($data)) {
            http_response_code(400);
            return ['error' => 'Geçersiz veya boş veri.'];
        }

        $updatedUser = $this->userModel->updateUser($id, $data, $isPatch);
        if ($updatedUser) {
            http_response_code(200);
            return $updatedUser;
        } else {
            http_response_code(404);
            return ['error' => 'Kullanıcı bulunamadı veya güncellenemedi.'];
        }
    }

    // DELETE
    public function delete(int $id) {
        if ($this->userModel->deleteUser($id)) {
            http_response_code(200);
            return ['message' => 'Kullanıcı başarıyla silindi.'];
        } else {
            http_response_code(404);
            return ['error' => 'Kullanıcı bulunamadı.'];
        }
    }
}
