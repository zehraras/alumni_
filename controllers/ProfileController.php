<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class ProfileController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    /**
     * Show authenticated user's profile and activities
     */
    public function showProfile(string $prefix = ''): void {
        $currentUser = AuthMiddleware::requireAuth($prefix . '/login');
        $userId = (int)$currentUser['id'];
        $userEmail = $currentUser['email'] ?? '';

        // Fetch user-specific applications and job postings
        $applications = $this->userModel->getApplications($userId, $userEmail);
        $jobPostings = $this->userModel->getJobPostings($userId);

        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $title = 'Profilim & Hesabım';
        $activePage = 'profile';

        ob_start();
        require __DIR__ . '/../views/user/profile.php';
        $content = ob_get_clean();

        require __DIR__ . '/../views/layout.php';
    }

    /**
     * Update authenticated user's profile details
     */
    public function updateProfile(string $prefix = ''): void {
        $currentUser = AuthMiddleware::requireAuth($prefix . '/login');
        $userId = (int)$currentUser['id'];

        $isim = trim($_POST['isim'] ?? '');
        $soyisim = trim($_POST['soyisim'] ?? '');
        $bolum = trim($_POST['bolum'] ?? '');
        $mezuniyetYili = !empty($_POST['mezuniyet_yili']) ? (int)$_POST['mezuniyet_yili'] : null;
        $sehir = trim($_POST['sehir'] ?? '');
        $pozisyon = trim($_POST['pozisyon'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';

        if (empty($isim)) {
            $_SESSION['flash_error'] = 'Ad alanı zorunludur.';
            header('Location: ' . $prefix . '/profile');
            exit;
        }

        $updateData = [
            'isim' => $isim,
            'soyisim' => $soyisim,
            'bolum' => $bolum,
            'mezuniyet_yili' => $mezuniyetYili,
            'sehir' => $sehir,
            'pozisyon' => $pozisyon,
        ];

        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                $_SESSION['flash_error'] = 'Yeni şifreniz en az 6 karakter olmalıdır.';
                header('Location: ' . $prefix . '/profile');
                exit;
            }
            $updateData['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $updated = $this->userModel->updateUser($userId, $updateData, true);
        if ($updated) {
            $_SESSION['user'] = $updated;
            $_SESSION['flash_success'] = 'Profil bilgileriniz başarıyla güncellendi.';
        } else {
            $_SESSION['flash_error'] = 'Profil güncellenirken bir hata oluştu.';
        }

        header('Location: ' . $prefix . '/profile');
        exit;
    }
}
