<?php

require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // READ ALL (HTML view)
    public function index() {
        // We will return data for the view
        return $this->userModel->getAllUsers();
    }

    // READ SINGLE (HTML view)
    public function show(int $id) {
        return $this->userModel->getUserById($id);
    }
    
    // In a full web UI, we might also have create/store, edit/update, destroy endpoints 
    // that return redirects and HTML forms. For now we expose the basic data functions 
    // that the view will use.
}
