<?php

require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    private UserModel $userModel;
    private string $prefix;

    public function __construct(string $prefix = '') {
        $this->userModel = new UserModel();
        $this->prefix = $prefix;
    }

    private function render(string $view, array $data = []) {
        extract($data);
        $prefix = $this->prefix;
        $activePage = $activePage ?? 'users';
        $title = $title ?? 'Alumni';
        
        ob_start();
        require __DIR__ . '/../views/' . $view . '.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/../views/layout.php';
    }

    // READ ALL (HTML view)
    public function index() {
        $users = $this->userModel->getAllUsers();
        $this->render('users/index', [
            'users' => $users,
            'title' => 'Kullanıcılar'
        ]);
    }

    // SHOW CREATE FORM
    public function create() {
        $this->render('users/create', [
            'title' => 'Yeni Mezun Ekle'
        ]);
    }

    // STORE NEW USER
    public function store(array $data) {
        $this->userModel->createUser($data);
        header('Location: ' . $this->prefix . '/users');
        exit;
    }

    // READ SINGLE (HTML view)
    public function show(int $id) {
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            echo "Kullanıcı bulunamadı.";
            exit;
        }
        $this->render('users/show', [
            'user' => $user,
            'title' => 'Kullanıcı Detayı'
        ]);
    }

    // SHOW EDIT FORM
    public function edit(int $id) {
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            echo "Kullanıcı bulunamadı.";
            exit;
        }
        $this->render('users/edit', [
            'user' => $user,
            'title' => 'Mezun Düzenle'
        ]);
    }

    // UPDATE USER
    public function update(int $id, array $data) {
        $this->userModel->updateUser($id, $data, true);
        header('Location: ' . $this->prefix . '/users/' . $id);
        exit;
    }

    // DELETE USER
    public function destroy(int $id) {
        $this->userModel->deleteUser($id);
        header('Location: ' . $this->prefix . '/users');
        exit;
    }
}
