<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class AuthController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    /**
     * Display registration page
     */
    public function showRegister(string $prefix = ''): void {
        if (AuthMiddleware::check()) {
            header('Location: ' . $prefix . '/profile');
            exit;
        }

        $error = $_SESSION['register_error'] ?? null;
        $formData = $_SESSION['register_data'] ?? [];
        unset($_SESSION['register_error'], $_SESSION['register_data']);

        $title = 'Kayıt Ol';
        $activePage = 'register';

        ob_start();
        require __DIR__ . '/../views/auth/register.php';
        $content = ob_get_clean();

        require __DIR__ . '/../views/layout.php';
    }

    /**
     * Handle registration submission
     */
    public function register(string $prefix = ''): void {
        $isim = trim($_POST['isim'] ?? '');
        $soyisim = trim($_POST['soyisim'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $bolum = trim($_POST['bolum'] ?? '');
        $mezuniyetYili = !empty($_POST['mezuniyet_yili']) ? (int)$_POST['mezuniyet_yili'] : null;
        $sehir = trim($_POST['sehir'] ?? '');
        $pozisyon = trim($_POST['pozisyon'] ?? '');
        $role = in_array($_POST['role'] ?? '', ['alumni', 'student']) ? $_POST['role'] : 'alumni';

        // Validation
        if (empty($isim) || empty($email) || empty($password)) {
            $_SESSION['register_error'] = 'Lütfen ad, e-posta ve şifre alanlarını doldurunuz.';
            $_SESSION['register_data'] = $_POST;
            header('Location: ' . $prefix . '/register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['register_error'] = 'Lütfen geçerli bir e-posta adresi giriniz.';
            $_SESSION['register_data'] = $_POST;
            header('Location: ' . $prefix . '/register');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['register_error'] = 'Şifreniz en az 6 karakter olmalıdır.';
            $_SESSION['register_data'] = $_POST;
            header('Location: ' . $prefix . '/register');
            exit;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['register_error'] = 'Girdiğiniz şifreler birbiriyle eşleşmiyor.';
            $_SESSION['register_data'] = $_POST;
            header('Location: ' . $prefix . '/register');
            exit;
        }

        // Check if email already registered
        $existing = $this->userModel->findByEmail($email);
        if ($existing) {
            $_SESSION['register_error'] = 'Bu e-posta adresiyle kayıtlı bir hesap zaten var. Lütfen giriş yapınız.';
            $_SESSION['register_data'] = $_POST;
            header('Location: ' . $prefix . '/register');
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $userData = [
            'isim' => $isim,
            'soyisim' => $soyisim,
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => $role,
            'bolum' => $bolum,
            'mezuniyet_yili' => $mezuniyetYili,
            'sehir' => $sehir,
            'pozisyon' => $pozisyon,
        ];

        $newUser = $this->userModel->createUser($userData);

        // Auto login
        AuthMiddleware::login($newUser);

        $_SESSION['flash_success'] = "Tebrikler {$isim}, kaydınız başarıyla oluşturuldu!";
        header('Location: ' . $prefix . '/profile');
        exit;
    }

    /**
     * Display login page
     */
    public function showLogin(string $prefix = ''): void {
        if (AuthMiddleware::check()) {
            $user = AuthMiddleware::user();
            if ($user['role'] === 'admin') {
                header('Location: ' . $prefix . '/admin');
            } else {
                header('Location: ' . $prefix . '/profile');
            }
            exit;
        }

        $error = $_SESSION['login_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        $redirect = $_GET['redirect'] ?? '';
        unset($_SESSION['login_error'], $_SESSION['flash_success']);

        $title = 'Giriş Yap';
        $activePage = 'login';

        ob_start();
        require __DIR__ . '/../views/auth/login.php';
        $content = ob_get_clean();

        require __DIR__ . '/../views/layout.php';
    }

    /**
     * Handle login submission
     */
    public function login(string $prefix = ''): void {
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $redirect = $_POST['redirect'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Lütfen e-posta adresinizi ve şifrenizi giriniz.';
            header('Location: ' . $prefix . '/login' . ($redirect ? '?redirect=' . urlencode($redirect) : ''));
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $_SESSION['login_error'] = 'Bu e-posta adresiyle eşleşen bir kullanıcı bulunamadı.';
            header('Location: ' . $prefix . '/login' . ($redirect ? '?redirect=' . urlencode($redirect) : ''));
            exit;
        }

        if (empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
            $_SESSION['login_error'] = 'Hatalı şifre girdiniz. Lütfen tekrar deneyiniz.';
            header('Location: ' . $prefix . '/login' . ($redirect ? '?redirect=' . urlencode($redirect) : ''));
            exit;
        }

        // Login successful
        AuthMiddleware::login($user, true);
        $_SESSION['flash_success'] = 'Başarıyla giriş yaptınız. Hoş geldiniz, ' . htmlspecialchars($user['isim'] ?? 'Kullanıcı') . '!';

        if (!empty($redirect) && str_starts_with($redirect, '/')) {
            header('Location: ' . $prefix . $redirect);
            exit;
        }

        if ($user['role'] === 'admin') {
            header('Location: ' . $prefix . '/admin');
        } else {
            header('Location: ' . $prefix . '/profile');
        }
        exit;
    }

    /**
     * Handle logout
     */
    public function logout(string $prefix = ''): void {
        AuthMiddleware::logout();
        $_SESSION['flash_success'] = 'Oturumunuz başarıyla kapatıldı.';
        header('Location: ' . $prefix . '/login');
        exit;
    }

    /**
     * API: POST /api/auth/register
     */
    public function apiRegister(array $input): array {
        $isim = trim($input['isim'] ?? $input['name'] ?? '');
        $soyisim = trim($input['soyisim'] ?? $input['surname'] ?? '');
        $email = trim(strtolower($input['email'] ?? ''));
        $password = $input['password'] ?? '';
        $role = in_array($input['role'] ?? '', ['admin', 'alumni', 'student']) ? $input['role'] : 'alumni';

        if (empty($isim) || empty($email) || empty($password)) {
            http_response_code(400);
            return ['status' => 'error', 'code' => 400, 'message' => 'isim, email and password are required.'];
        }

        if ($this->userModel->findByEmail($email)) {
            http_response_code(409);
            return ['status' => 'error', 'code' => 409, 'message' => 'Email is already registered.'];
        }

        $token = bin2hex(random_bytes(32));
        $userData = [
            'isim' => $isim,
            'soyisim' => $soyisim,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
            'auth_token' => $token,
            'bolum' => $input['bolum'] ?? $input['department'] ?? '',
            'mezuniyet_yili' => $input['mezuniyet_yili'] ?? $input['graduationYear'] ?? null,
            'sehir' => $input['sehir'] ?? $input['city'] ?? '',
            'pozisyon' => $input['pozisyon'] ?? $input['position'] ?? '',
        ];

        $user = $this->userModel->createUser($userData);
        unset($user['password_hash']);

        http_response_code(201);
        return [
            'status' => 'success',
            'code' => 201,
            'message' => 'User registered successfully.',
            'token' => $token,
            'user' => $user
        ];
    }

    /**
     * API: POST /api/auth/login
     */
    public function apiLogin(array $input): array {
        $email = trim(strtolower($input['email'] ?? ''));
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            http_response_code(400);
            return ['status' => 'error', 'code' => 400, 'message' => 'email and password are required.'];
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
            http_response_code(401);
            return ['status' => 'error', 'code' => 401, 'message' => 'Invalid email or password.'];
        }

        $token = bin2hex(random_bytes(32));
        $this->userModel->updateToken((int)$user['id'], $token);

        unset($user['password_hash']);
        $user['auth_token'] = $token;

        return [
            'status' => 'success',
            'code' => 200,
            'message' => 'Authentication successful.',
            'token' => $token,
            'user' => $user
        ];
    }

    /**
     * API: POST /api/auth/logout
     */
    public function apiLogout(): array {
        $user = AuthMiddleware::user();
        if ($user && !empty($user['id'])) {
            $this->userModel->updateToken((int)$user['id'], null);
        }
        AuthMiddleware::logout();

        return ['status' => 'success', 'code' => 200, 'message' => 'Logged out successfully.'];
    }

    /**
     * API: GET /api/auth/me
     */
    public function apiMe(): array {
        $user = AuthMiddleware::requireAuth();
        unset($user['password_hash']);
        return [
            'status' => 'success',
            'code' => 200,
            'user' => $user
        ];
    }
}
