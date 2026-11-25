<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/JobPostingModel.php';
require_once __DIR__ . '/../models/AnnouncementModel.php';
require_once __DIR__ . '/../models/ApplicationModel.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class AdminController {
    private UserModel $userModel;
    private JobPostingModel $jobPostingModel;
    private AnnouncementModel $announcementModel;
    private ApplicationModel $applicationModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->jobPostingModel = new JobPostingModel();
        $this->announcementModel = new AnnouncementModel();
        $this->applicationModel = new ApplicationModel();
    }

    /**
     * Admin Dashboard
     */
    public function dashboard(string $prefix = ''): void {
        // Enforce Admin role
        $currentUser = AuthMiddleware::requireRole('admin', $prefix . '/login');

        $users = $this->userModel->getAllUsers();
        $jobPostings = $this->jobPostingModel->getAll();
        $announcements = $this->announcementModel->getAll();
        $applications = $this->applicationModel->getAll();

        // Calculate counts
        $totalUsers = count($users);
        $totalJobs = count($jobPostings);
        $totalAnnouncements = count($announcements);
        $totalApplications = count($applications);

        $adminCount = 0;
        $alumniCount = 0;
        $studentCount = 0;
        foreach ($users as $u) {
            $r = $u['role'] ?? 'alumni';
            if ($r === 'admin') $adminCount++;
            elseif ($r === 'student') $studentCount++;
            else $alumniCount++;
        }

        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $title = 'Yönetim Paneli (Admin Panel)';
        $activePage = 'admin';

        ob_start();
        require __DIR__ . '/../views/admin/dashboard.php';
        $content = ob_get_clean();

        require __DIR__ . '/../views/layout.php';
    }

    /**
     * Update user role (e.g. promote to admin, assign alumni or student)
     */
    public function updateRole(string $prefix = ''): void {
        AuthMiddleware::requireRole('admin', $prefix . '/login');

        $userId = (int)($_POST['user_id'] ?? 0);
        $newRole = trim($_POST['role'] ?? '');

        if ($userId <= 0 || !in_array($newRole, ['admin', 'alumni', 'student'])) {
            $_SESSION['flash_error'] = 'Geçersiz kullanıcı veya rol seçimi.';
            header('Location: ' . $prefix . '/admin');
            exit;
        }

        $success = $this->userModel->updateRole($userId, $newRole);
        if ($success) {
            $_SESSION['flash_success'] = "Kullanıcı ID #{$userId} rolü başarıyla '{$newRole}' olarak güncellendi.";
        } else {
            $_SESSION['flash_error'] = "Rol güncellenirken bir hata oluştu.";
        }

        header('Location: ' . $prefix . '/admin');
        exit;
    }

    /**
     * Delete user from system
     */
    public function deleteUser(string $prefix = ''): void {
        $currentUser = AuthMiddleware::requireRole('admin', $prefix . '/login');

        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            $_SESSION['flash_error'] = 'Geçersiz kullanıcı ID.';
            header('Location: ' . $prefix . '/admin');
            exit;
        }

        if ($userId === (int)$currentUser['id']) {
            $_SESSION['flash_error'] = 'Kendi yönetici hesabınızı silemezsiniz.';
            header('Location: ' . $prefix . '/admin');
            exit;
        }

        $success = $this->userModel->deleteUser($userId);
        if ($success) {
            $_SESSION['flash_success'] = "Kullanıcı ID #{$userId} başarıyla silindi.";
        } else {
            $_SESSION['flash_error'] = "Kullanıcı silinemedi.";
        }

        header('Location: ' . $prefix . '/admin');
        exit;
    }
}
