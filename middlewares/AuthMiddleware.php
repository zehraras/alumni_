<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthMiddleware {
    private static ?array $currentUser = null;
    private static bool $initialized = false;

    /**
     * Initialize session and identify authenticated user
     */
    public static function init(): void {
        if (self::$initialized) return;

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        $userModel = new UserModel();

        // 1. Check active session
        if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
            self::$currentUser = $_SESSION['user'];
        }

        // 2. Check HTTP Bearer token or X-Auth-Token header
        $token = self::extractBearerToken();
        if (!$token && !empty($_COOKIE['alumni_auth_token'])) {
            $token = $_COOKIE['alumni_auth_token'];
        }

        if ($token && empty(self::$currentUser)) {
            $user = $userModel->findByToken($token);
            if ($user) {
                self::$currentUser = $user;
                $_SESSION['user'] = $user;
            }
        }

        // Refresh user data from DB if id exists to keep role & state in sync
        if (!empty(self::$currentUser['id'])) {
            $fresh = $userModel->getUserById((int)self::$currentUser['id']);
            if ($fresh) {
                self::$currentUser = $fresh;
                $_SESSION['user'] = $fresh;
            }
        }

        self::$initialized = true;
    }

    /**
     * Extract Bearer token from headers
     */
    public static function extractBearerToken(): ?string {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (empty($header) && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
            return $matches[1];
        }

        if (!empty($_SERVER['HTTP_X_AUTH_TOKEN'])) {
            return $_SERVER['HTTP_X_AUTH_TOKEN'];
        }

        return null;
    }

    /**
     * Check if user is authenticated
     */
    public static function check(): bool {
        self::init();
        return !empty(self::$currentUser);
    }

    /**
     * Get authenticated user array
     */
    public static function user(): ?array {
        self::init();
        return self::$currentUser;
    }

    /**
     * Get authenticated user ID
     */
    public static function id(): ?int {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    /**
     * Get authenticated user's role
     */
    public static function role(): ?string {
        $user = self::user();
        return $user['role'] ?? null;
    }

    /**
     * Check if current user is admin
     */
    public static function isAdmin(): bool {
        return self::role() === 'admin';
    }

    /**
     * Check if current user is alumni
     */
    public static function isAlumni(): bool {
        return self::role() === 'alumni';
    }

    /**
     * Check if current user is student
     */
    public static function isStudent(): bool {
        return self::role() === 'student';
    }

    /**
     * Log user in, generate token, update session and cookie
     */
    public static function login(array $user, bool $remember = true): string {
        self::init();

        $token = bin2hex(random_bytes(32));
        $userModel = new UserModel();
        $userModel->updateToken((int)$user['id'], $token);

        $user['auth_token'] = $token;
        self::$currentUser = $user;
        $_SESSION['user'] = $user;

        if ($remember && !headers_sent()) {
            setcookie('alumni_auth_token', $token, [
                'expires' => time() + (86400 * 30), // 30 days
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }

        return $token;
    }

    /**
     * Log user out and destroy session/token
     */
    public static function logout(): void {
        self::init();

        if (self::$currentUser && !empty(self::$currentUser['id'])) {
            $userModel = new UserModel();
            $userModel->updateToken((int)self::$currentUser['id'], null);
        }

        self::$currentUser = null;
        $_SESSION['user'] = null;
        unset($_SESSION['user']);

        if (!headers_sent()) {
            setcookie('alumni_auth_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'httponly' => true
            ]);
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Require authentication; redirect or return 401
     */
    public static function requireAuth(string $redirect = '/login'): ?array {
        self::init();

        if (!self::check()) {
            if (self::isApiRequest()) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'status' => 'error',
                    'code' => 401,
                    'message' => 'Unauthorized: Please authenticate to access this resource.'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                $target = '/login?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? '/');
                header("Location: $target");
                exit;
            }
        }

        return self::$currentUser;
    }

    /**
     * Require specific role; redirect or return 403
     */
    public static function requireRole(string|array $roles, string $redirect = '/'): ?array {
        $user = self::requireAuth();

        $allowedRoles = is_array($roles) ? $roles : [$roles];
        $currentRole = self::role();

        if (!in_array($currentRole, $allowedRoles, true)) {
            if (self::isApiRequest()) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Forbidden: You do not have permission to perform this action.',
                    'required_role' => $allowedRoles,
                    'current_role' => $currentRole
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                $_SESSION['flash_error'] = 'Bu alana erişmek için yönetici (Admin) yetkisine sahip olmalısınız.';
                header("Location: $redirect");
                exit;
            }
        }

        return $user;
    }

    /**
     * Determine if request is an API call
     */
    private static function isApiRequest(): bool {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (str_contains($uri, '/api/')) return true;

        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (str_contains($accept, 'application/json')) return true;

        return false;
    }
}
