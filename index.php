<?php

// Alumni Management System - Week 02 & Week 03 & Week 04
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/ApiUserController.php';
require_once __DIR__ . '/controllers/AnnouncementController.php';
require_once __DIR__ . '/controllers/ApiAnnouncementController.php';
require_once __DIR__ . '/controllers/ApiJobPostingController.php';
require_once __DIR__ . '/controllers/ApiApplicationController.php';
require_once __DIR__ . '/controllers/JobPostingController.php';
require_once __DIR__ . '/controllers/ApplicationController.php';
require_once __DIR__ . '/middlewares/LoggerMiddleware.php';
require_once __DIR__ . '/middlewares/GuardianMiddleware.php';
require_once __DIR__ . '/middlewares/AuthMiddleware.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/ProfileController.php';

// Initialize session and authentication state
AuthMiddleware::init();

// Step 5 & 6: 5xx Guardian & error logger registration
GuardianMiddleware::register();

// Step 3: Logger Middleware - logs every request on terminal upon completion
$requestLogger = new LoggerMiddleware();
register_shutdown_function(function() use ($requestLogger) {
    $code = http_response_code() ?: 200;
    $requestLogger->log($code);
});

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Parse path without query string
$path = parse_url($requestUri, PHP_URL_PATH);

// Support both direct routes and /alumni prefix
$prefix = '';
if (str_starts_with($path, '/alumni')) {
    $prefix = '/alumni';
    $path = substr($path, strlen('/alumni'));
    if ($path === '' || $path === false) {
        $path = '/';
    }
}

// Normalize trailing slash (except root)
if (strlen($path) > 1 && str_ends_with($path, '/')) {
    $path = rtrim($path, '/');
}



// Helper: Parse request body for POST/PUT/PATCH (supports JSON, form-data, urlencoded)
function parseRequestBody() {
    $rawInput = file_get_contents('php://input');
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    // 1. Try JSON
    $data = json_decode($rawInput, true);
    if (is_array($data) && !empty($data)) {
        return $data;
    }

    // 2. Check $_POST
    if (!empty($_POST)) {
        return $_POST;
    }

    // 3. Parse multipart/form-data for PUT and PATCH requests
    if (stripos($contentType, 'multipart/form-data') !== false) {
        $data = [];
        if (preg_match('/boundary=(.*)$/i', $contentType, $matches)) {
            $boundary = trim($matches[1], '" ');
            $blocks = explode('--' . $boundary, $rawInput);
            foreach ($blocks as $block) {
                if (trim($block) === '' || trim($block) === '--') continue;
                if (preg_match('/name="([^"]+)"(?:\r?\n){2}(.*?)(?:\r?\n)?$/s', $block, $m)) {
                    $name = $m[1];
                    $value = trim($m[2]);
                    $data[$name] = $value;
                }
            }
        }
        if (!empty($data)) {
            return $data;
        }
    }

    // 4. Try x-www-form-urlencoded
    $parsed = [];
    parse_str($rawInput, $parsed);
    if (is_array($parsed) && !empty($parsed)) {
        return $parsed;
    }

    return [];
}

// Base styling for browser views using unified layout.php
function renderLayout($title, $content, $prefix = '', $activePage = '') {
    ob_start();
    require __DIR__ . '/views/layout.php';
    return ob_get_clean();
}

// -------------------------------------------------------------
// Step 1 & 5: GET / -> Main Page
// -------------------------------------------------------------
if ($path === '/') {
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (isset($_GET['raw']) || str_contains($accept, 'text/plain')) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "ok";
        exit;
    }

    $userModel = new UserModel();
    $announcementModel = new AnnouncementModel();
    $jobPostingModel = new JobPostingModel();
    $applicationModel = new ApplicationModel();

    $userCount = count($userModel->getAllUsers());
    $announcementCount = count($announcementModel->getAll());
    $jobCount = count($jobPostingModel->getAll());
    $appCount = count($applicationModel->getAll());

    header('Content-Type: text/html; charset=utf-8');
    $content = <<<HTML
        <!-- Hero Section -->
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%); color: white; padding: 3rem 2.5rem; border-radius: 16px; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.25);">
            <div style="max-width: 650px;">
                <span style="display: inline-block; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem;">
                    🎓 Üniversite Mezunları Ağı
                </span>
                <h1 style="font-size: 2.25rem; font-weight: 800; line-height: 1.2; margin-bottom: 1rem; color: #ffffff;">
                    Alumni Management System
                </h1>
                <p style="font-size: 1.05rem; opacity: 0.95; line-height: 1.6; margin-bottom: 1.5rem;">
                    Mezunlarımız arasındaki bağı güçlendiren, kariyer fırsatlarını buluşturan ve üniversite ekosistemini canlı tutan yeni nesil mezun yönetim platformu.
                </p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="{$prefix}/users" style="background: #ffffff; color: #1e40af; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">👥 Mezunları Keşfet</a>
                    <a href="{$prefix}/job-postings" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none;">💼 İş İlanları</a>
                    <a href="{$prefix}/announcements" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none;">📢 Duyurular</a>
                </div>
            </div>
        </div>

        <!-- Live Statistics Counter Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
            <div class="card" style="margin-bottom: 0; padding: 1.5rem; border-top: 4px solid #2563eb;">
                <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Kayıtlı Mezun</div>
                <div style="font-size: 2.25rem; font-weight: 800; color: #1e3a8a; margin: 0.5rem 0;">{$userCount}</div>
                <div style="font-size: 0.85rem; color: #059669; font-weight: 500;">✓ Aktif ve güncel profil</div>
            </div>
            <div class="card" style="margin-bottom: 0; padding: 1.5rem; border-top: 4px solid #16a34a;">
                <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Bölüm Duyuruları</div>
                <div style="font-size: 2.25rem; font-weight: 800; color: #166534; margin: 0.5rem 0;">{$announcementCount}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Son etkinlikler ve bültenler</div>
            </div>
            <div class="card" style="margin-bottom: 0; padding: 1.5rem; border-top: 4px solid #0284c7;">
                <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Kariyer Fırsatları</div>
                <div style="font-size: 2.25rem; font-weight: 800; color: #075985; margin: 0.5rem 0;">{$jobCount}</div>
                <div style="font-size: 0.85rem; color: #2563eb; font-weight: 500;">İş ve staj pozisyonu</div>
            </div>
            <div class="card" style="margin-bottom: 0; padding: 1.5rem; border-top: 4px solid #eab308;">
                <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Aday Başvuruları</div>
                <div style="font-size: 2.25rem; font-weight: 800; color: #854d0e; margin: 0.5rem 0;">{$appCount}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Değerlendirilen başvuru</div>
            </div>
        </div>

        <!-- Quick Modules Overview -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="card" style="margin-bottom: 0;">
                <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">👨‍🎓</div>
                <h3 style="margin-bottom: 0.5rem;">Mezun Rehberi</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem;">
                    Bölüm mezunlarımızı görüntüleyin, filtreleyin veya yeni bir mezun kartı oluşturun.
                </p>
                <a href="{$prefix}/users" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Mezunları Yönet &rarr;</a>
            </div>
            <div class="card" style="margin-bottom: 0;">
                <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">💼</div>
                <h3 style="margin-bottom: 0.5rem;">Kariyer & İlanlar</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem;">
                    Şirketlerin açık iş pozisyonlarını inceleyin, doğrudan CV veya portföy bağlantınızla başvurun.
                </p>
                <a href="{$prefix}/job-postings" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #0284c7;">İlanları İncele &rarr;</a>
            </div>
            <div class="card" style="margin-bottom: 0;">
                <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">📢</div>
                <h3 style="margin-bottom: 0.5rem;">Haberler & Duyurular</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem;">
                    Mezunlar derneği etkinlikleri, konferanslar ve gala geceleri hakkında duyuruları takip edin.
                </p>
                <a href="{$prefix}/announcements" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #16a34a;">Duyuruları Gör &rarr;</a>
            </div>
        </div>

        <!-- API & Docs Helper -->
        <div class="card">
            <span class="badge" style="background: #fef3c7; color: #92400e;">⚡ Geliştirici & API Portalı</span>
            <h3 style="margin-top: 0.5rem; margin-bottom: 0.5rem;">Sistem Servisleri & Rotalar</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">Postman veya harici entegrasyonlar için sunulan REST API uç noktaları:</p>
            <div class="routes-grid">
                <div class="route-item">
                    <span class="route-method">GET (DOCS)</span>
                    <a href="{$prefix}/api/swagger" class="route-path">/api/swagger</a>
                    <span class="route-desc">Otomatik API Dokümantasyonu (OpenAPI / Swagger)</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET/POST</span>
                    <a href="{$prefix}/api/job-postings" class="route-path">/api/job-postings</a>
                    <span class="route-desc">İş ilanları REST servisi</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET/POST</span>
                    <a href="{$prefix}/api/applications" class="route-path">/api/applications</a>
                    <span class="route-desc">İş başvuruları REST servisi</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET</span>
                    <a href="{$prefix}/api/health" class="route-path">/api/health</a>
                    <span class="route-desc">Sistem sağlık ve durum kontrolü</span>
                </div>
            </div>
        </div>
HTML;
    echo renderLayout("Ana Sayfa", $content, $prefix, 'home');
    exit;
}

// -------------------------------------------------------------
// Authentication, Administration & User Profile Controllers
// -------------------------------------------------------------
$authController = new AuthController();
$adminController = new AdminController();
$profileController = new ProfileController();

// Auth Routes: Register
if ($path === '/register' && $method === 'GET') {
    $authController->showRegister($prefix);
    exit;
}
if ($path === '/register' && $method === 'POST') {
    $authController->register($prefix);
    exit;
}

// Auth Routes: Login & Logout
if ($path === '/login' && $method === 'GET') {
    $authController->showLogin($prefix);
    exit;
}
if ($path === '/login' && $method === 'POST') {
    $authController->login($prefix);
    exit;
}
if (($path === '/logout') && ($method === 'GET' || $method === 'POST')) {
    $authController->logout($prefix);
    exit;
}

// Admin Panel Routes (Protected by AuthMiddleware & Admin Role)
if ($path === '/admin' && $method === 'GET') {
    $adminController->dashboard($prefix);
    exit;
}
if ($path === '/admin/users/role' && $method === 'POST') {
    $adminController->updateRole($prefix);
    exit;
}
if ($path === '/admin/users/delete' && $method === 'POST') {
    $adminController->deleteUser($prefix);
    exit;
}

// User-Specific Pages (Protected by AuthMiddleware)
if ($path === '/profile' && $method === 'GET') {
    $profileController->showProfile($prefix);
    exit;
}
if ($path === '/profile/update' && $method === 'POST') {
    $profileController->updateProfile($prefix);
    exit;
}

// -------------------------------------------------------------
// Web Interface Routes (Week 04 - Steps 5 & 6)
// -------------------------------------------------------------
$userController = new UserController($prefix);

// GET /users -> Listing (Read)
if ($path === '/users' && $method === 'GET') {
    $userController->index();
    exit;
}

// GET /users/create -> Show create form
if ($path === '/users/create' && $method === 'GET') {
    $userController->create();
    exit;
}

// POST /users -> Create
if ($path === '/users' && $method === 'POST') {
    $data = parseRequestBody();
    $userController->store($data);
    exit;
}

// GET /users/{id} -> Single view (Read)
if (preg_match('#^/users/(\d+)$#', $path, $matches) && $method === 'GET') {
    $userController->show((int)$matches[1]);
    exit;
}

// GET /users/{id}/edit -> Show edit form
if (preg_match('#^/users/(\d+)/edit$#', $path, $matches) && $method === 'GET') {
    $userController->edit((int)$matches[1]);
    exit;
}

// POST /users/{id}/update -> Update
if (preg_match('#^/users/(\d+)/update$#', $path, $matches) && $method === 'POST') {
    $data = parseRequestBody();
    $userController->update((int)$matches[1], $data);
    exit;
}

// POST /users/{id}/delete -> Delete
if (preg_match('#^/users/(\d+)/delete$#', $path, $matches) && $method === 'POST') {
    $userController->destroy((int)$matches[1]);
    exit;
}

// -------------------------------------------------------------
// Announcement Web Routes
// -------------------------------------------------------------
$announcementController = new AnnouncementController($prefix);

if ($path === '/announcements' && $method === 'GET') { $announcementController->index(); exit; }
if ($path === '/announcements/create' && $method === 'GET') { $announcementController->create(); exit; }
if ($path === '/announcements' && $method === 'POST') { $announcementController->store(parseRequestBody()); exit; }
if (preg_match('#^/announcements/(\d+)$#', $path, $matches) && $method === 'GET') { $announcementController->show((int)$matches[1]); exit; }
if (preg_match('#^/announcements/(\d+)/edit$#', $path, $matches) && $method === 'GET') { $announcementController->edit((int)$matches[1]); exit; }
if (preg_match('#^/announcements/(\d+)/update$#', $path, $matches) && $method === 'POST') { $announcementController->update((int)$matches[1], parseRequestBody()); exit; }
if (preg_match('#^/announcements/(\d+)/delete$#', $path, $matches) && $method === 'POST') { $announcementController->destroy((int)$matches[1]); exit; }

// -------------------------------------------------------------
// Job Postings Web Routes (Homework)
// -------------------------------------------------------------
$jobPostingController = new JobPostingController($prefix);
if ($path === '/job-postings' && $method === 'GET') { $jobPostingController->index(); exit; }
if ($path === '/job-postings/create' && $method === 'GET') { $jobPostingController->create(); exit; }
if ($path === '/job-postings/create' && $method === 'POST') { $jobPostingController->store(parseRequestBody()); exit; }
if (preg_match('#^/job-postings/(\d+)$#', $path, $matches) && $method === 'GET') { $jobPostingController->show((int)$matches[1]); exit; }
if (preg_match('#^/job-postings/(\d+)/apply$#', $path, $matches) && $method === 'POST') { $jobPostingController->apply((int)$matches[1], parseRequestBody()); exit; }

// -------------------------------------------------------------
// Applications Web Routes (Homework)
// -------------------------------------------------------------
$applicationController = new ApplicationController($prefix);
if ($path === '/applications' && $method === 'GET') { $applicationController->index(); exit; }
if (preg_match('#^/applications/(\d+)$#', $path, $matches) && $method === 'GET') { $applicationController->show((int)$matches[1]); exit; }


// -------------------------------------------------------------
// Authentication REST API Endpoints
// -------------------------------------------------------------
if (($path === '/api/auth/register' || $path === '/api/register') && $method === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($authController->apiRegister(parseRequestBody()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if (($path === '/api/auth/login' || $path === '/api/login') && $method === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($authController->apiLogin(parseRequestBody()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($path === '/api/auth/logout' && ($method === 'POST' || $method === 'GET')) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($authController->apiLogout(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if (($path === '/api/auth/me' || $path === '/api/me') && $method === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($authController->apiMe(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// -------------------------------------------------------------
// Week 03 - Step 1: GET /api/health -> JSON
// -------------------------------------------------------------
if ($path === '/api/health') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'ok'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// -------------------------------------------------------------
// Week 03 - Step 7: GET /api/swagger -> API Documentation
// -------------------------------------------------------------
if ($path === '/api/swagger') {
    header('Content-Type: application/json; charset=utf-8');
    
    $swaggerDoc = [
        'openapi' => '3.0.0',
        'info' => [
            'title' => 'Alumni System API',
            'description' => 'Mezun takip sistemi API dokümantasyonu. Bu doküman otomatik olarak güncellenir (Week 3 - Step 7).',
            'version' => '1.0.0'
        ],
        'paths' => [
            '/users' => [
                'get' => [
                    'summary' => 'Web Arayüzü: Tüm mezunları listele',
                    'responses' => ['200' => ['description' => 'Mezun listesi HTML sayfası']]
                ]
            ],
            '/users/{id}' => [
                'get' => [
                    'summary' => 'Web Arayüzü: Tekil mezun profili',
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                    'responses' => ['200' => ['description' => 'Mezun profili HTML sayfası']]
                ]
            ],
            '/api/health' => [
                'get' => [
                    'summary' => 'Sistem sağlık durumu kontrolü',
                    'responses' => [
                        '200' => [
                            'description' => 'Sistem çalışıyor',
                            'content' => ['application/json' => ['example' => ['status' => 'ok']]]
                        ]
                    ]
                ]
            ],
            '/api/users' => [
                'get' => [
                    'summary' => 'Tüm mezunları listele',
                    'responses' => ['200' => ['description' => 'Mezun listesi (JSON array)']]
                ],
                'post' => [
                    'summary' => 'Yeni mezun ekle',
                    'description' => 'Dinamik alanlar kabul edilir (İsim, Soyisim, vb.)',
                    'responses' => ['201' => ['description' => 'Başarıyla oluşturuldu']]
                ]
            ],
            '/api/users/{id}' => [
                'get' => [
                    'summary' => 'ID\'ye göre tekil mezun getir',
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                    'responses' => ['200' => ['description' => 'Mezun detayı']]
                ],
                'put' => [
                    'summary' => 'Mezun bilgilerini güncelle',
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                    'responses' => ['200' => ['description' => 'Güncellenmiş mezun detayı']]
                ],
                'patch' => [
                    'summary' => 'Mezun bilgilerini kısmi güncelle',
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                    'responses' => ['200' => ['description' => 'Güncellenmiş mezun detayı']]
                ],
                'delete' => [
                    'summary' => 'Mezun sil',
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                    'responses' => ['200' => ['description' => 'Başarıyla silindi']]
                ]
            ]
        ]
    ];
    
    echo json_encode($swaggerDoc, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}


// -------------------------------------------------------------
// Week 03 - Step 3: POST & GET /api/users -> JSON
// -------------------------------------------------------------
if ($path === '/api/users') {
    header('Content-Type: application/json; charset=utf-8');

    $apiController = new ApiUserController();
    
    if ($method === 'POST') {
        $data = parseRequestBody();
        $response = $apiController->create($data);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($method === 'GET') {
        $response = $apiController->index();
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------------------------------------------
// Week 03 - Step 5: PUT / PATCH /api/users/{id} -> Update user
// -------------------------------------------------------------
if (preg_match('#^/api/users/([^/]+)$#', $path, $matches)) {
    header('Content-Type: application/json; charset=utf-8');
    $id = $matches[1];

    $apiController = new ApiUserController();
    
    // PUT or PATCH: Update user
    if ($method === 'PUT' || $method === 'PATCH') {
        $data = parseRequestBody();
        $isPatch = ($method === 'PATCH');
        $response = $apiController->update((int)$id, $data, $isPatch);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // GET single user: /api/users/{id}
    if ($method === 'GET') {
        $response = $apiController->show((int)$id);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Week 03 - Step 6: DELETE /api/users/{id} -> Delete user
    if ($method === 'DELETE') {
        $response = $apiController->delete((int)$id);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------------------------------------------
// Announcement API Routes (Homework)
// -------------------------------------------------------------
if ($path === '/api/announcements') {
    header('Content-Type: application/json; charset=utf-8');
    $apiAnnouncementController = new ApiAnnouncementController();
    if ($method === 'POST') {
        echo json_encode($apiAnnouncementController->create(parseRequestBody()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'GET') {
        echo json_encode($apiAnnouncementController->index(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (preg_match('#^/api/announcements/(\d+)$#', $path, $matches)) {
    header('Content-Type: application/json; charset=utf-8');
    $id = (int)$matches[1];
    $apiAnnouncementController = new ApiAnnouncementController();
    
    if ($method === 'GET') {
        echo json_encode($apiAnnouncementController->show($id), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'PUT' || $method === 'PATCH') {
        echo json_encode($apiAnnouncementController->update($id, parseRequestBody(), $method === 'PATCH'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'DELETE') {
        echo json_encode($apiAnnouncementController->delete($id), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------------------------------------------
// Job Postings API Routes (Direct & Nested)
// -------------------------------------------------------------

// Nested Route: /api/users/{user_id}/job-postings
if (preg_match('#^/api/users/(\d+)/job-postings$#', $path, $matches)) {
    header('Content-Type: application/json; charset=utf-8');
    $userId = (int)$matches[1];
    $apiJobPostingController = new ApiJobPostingController();

    if ($method === 'GET') {
        echo json_encode($apiJobPostingController->userJobPostings($userId), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'POST') {
        echo json_encode($apiJobPostingController->createUserJobPosting($userId, parseRequestBody()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Direct Route: /api/job-postings
if ($path === '/api/job-postings') {
    header('Content-Type: application/json; charset=utf-8');
    $apiJobPostingController = new ApiJobPostingController();

    if ($method === 'GET') {
        echo json_encode($apiJobPostingController->index(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'POST') {
        echo json_encode($apiJobPostingController->create(parseRequestBody()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Direct Route: /api/job-postings/{id}
if (preg_match('#^/api/job-postings/(\d+)$#', $path, $matches)) {
    header('Content-Type: application/json; charset=utf-8');
    $id = (int)$matches[1];
    $apiJobPostingController = new ApiJobPostingController();

    if ($method === 'GET') {
        echo json_encode($apiJobPostingController->show($id), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'PUT' || $method === 'PATCH') {
        echo json_encode($apiJobPostingController->update($id, parseRequestBody(), $method === 'PATCH'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'DELETE') {
        echo json_encode($apiJobPostingController->delete($id), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------------------------------------------
// Applications API Routes (Direct & Nested)
// -------------------------------------------------------------

// Nested Route: /api/job-postings/{job_id}/applications
if (preg_match('#^/api/job-postings/(\d+)/applications$#', $path, $matches)) {
    header('Content-Type: application/json; charset=utf-8');
    $jobId = (int)$matches[1];
    $apiApplicationController = new ApiApplicationController();

    if ($method === 'GET') {
        echo json_encode($apiApplicationController->jobPostingApplications($jobId), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'POST') {
        echo json_encode($apiApplicationController->createJobPostingApplication($jobId, parseRequestBody()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Nested Route: /api/users/{user_id}/applications
if (preg_match('#^/api/users/(\d+)/applications$#', $path, $matches)) {
    header('Content-Type: application/json; charset=utf-8');
    $userId = (int)$matches[1];
    $apiApplicationController = new ApiApplicationController();

    if ($method === 'GET') {
        echo json_encode($apiApplicationController->userApplications($userId), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Direct Route: /api/applications
if ($path === '/api/applications') {
    header('Content-Type: application/json; charset=utf-8');
    $apiApplicationController = new ApiApplicationController();

    if ($method === 'GET') {
        echo json_encode($apiApplicationController->index(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'POST') {
        echo json_encode($apiApplicationController->create(parseRequestBody()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Direct Route: /api/applications/{id}
if (preg_match('#^/api/applications/(\d+)$#', $path, $matches)) {
    header('Content-Type: application/json; charset=utf-8');
    $id = (int)$matches[1];
    $apiApplicationController = new ApiApplicationController();

    if ($method === 'GET') {
        echo json_encode($apiApplicationController->show($id), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'PUT' || $method === 'PATCH') {
        echo json_encode($apiApplicationController->update($id, parseRequestBody(), $method === 'PATCH'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($method === 'DELETE') {
        echo json_encode($apiApplicationController->delete($id), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}



// -------------------------------------------------------------
// Week 02 - Step 6: GET /about -> Temporary about page
// -------------------------------------------------------------
if ($path === '/about') {
    header('Content-Type: text/html; charset=utf-8');
    $content = <<<HTML
        <div class="card" style="margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <span class="badge">ℹ️ About the Platform</span>
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Version 2.0.0 &bull; Autumn 2026</span>
            </div>

            <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 1rem; letter-spacing: -0.5px;">
                Alumni Management System
            </h1>
            <p style="font-size: 1.05rem; color: var(--text-muted); line-height: 1.7; max-width: 720px; margin-bottom: 2rem;">
                A centralized, modern platform built to bridge connections among graduates, university faculty, and industry partners. Track alumni career trajectories, discover collaborative opportunities, and foster an engaged lifelong community.
            </p>

            <hr style="border: none; border-top: 1px solid var(--border); margin: 2rem 0;">

            <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 1.25rem;">
                System Specifications & Architecture
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem;">
                    <div style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 0.35rem;">Runtime</div>
                    <div style="font-weight: 700; color: var(--text-main); font-size: 1.05rem;">PHP 8.2 CLI</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Built-in web server with clean routing</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem;">
                    <div style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 0.35rem;">Persistence</div>
                    <div style="font-weight: 700; color: var(--text-main); font-size: 1.05rem;">PostgreSQL 15</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Relational DB + JSON fallback engine</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem;">
                    <div style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 0.35rem;">Infrastructure</div>
                    <div style="font-weight: 700; color: var(--text-main); font-size: 1.05rem;">Docker Compose</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Zero-config reproducible containers</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem;">
                    <div style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 0.35rem;">Engineering AI</div>
                    <div style="font-weight: 700; color: var(--text-main); font-size: 1.05rem;">Antigravity</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">DeepMind agentic pair programmer</div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <a href="{$prefix}/" class="btn btn-secondary">&larr; Return to Dashboard</a>
                <a href="{$prefix}/users" class="btn">Explore Alumni Directory &rarr;</a>
            </div>
        </div>
HTML;
    echo renderLayout("About", $content, $prefix, 'about');
    exit;
}

// -------------------------------------------------------------
// Step 2: GET /hello -> "Hello, World!"
// -------------------------------------------------------------
if ($path === '/hello') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Hello, World!";
    exit;
}

// -------------------------------------------------------------
// Step 3: GET /hello/{name} -> "Hello, {Name}!"
// -------------------------------------------------------------
if (preg_match('#^/hello/([^/]+)$#', $path, $matches)) {
    header('Content-Type: text/plain; charset=utf-8');
    $rawName = urldecode($matches[1]);
    $formattedName = ucfirst($rawName);
    echo "Hello, {$formattedName}!";
    exit;
}

// -------------------------------------------------------------
// Step 4: GET /sum/{number1}/{number2} -> sum
// -------------------------------------------------------------
if (preg_match('#^/sum/([^/]+)/([^/]+)$#', $path, $matches)) {
    header('Content-Type: text/plain; charset=utf-8');
    $num1 = $matches[1];
    $num2 = $matches[2];

    if (is_numeric($num1) && is_numeric($num2)) {
        $sum = $num1 + $num2;
        echo $sum;
    } else {
        http_response_code(400);
        echo "Error: Parameters must be numbers";
    }
    exit;
}

// -------------------------------------------------------------
// Step 5: Test Route for 5xx Guardian
// -------------------------------------------------------------
if ($path === '/api/crash-test' || $path === '/crash-test') {
    throw new RuntimeException("5xx Guardian Test: Simulated internal server error.");
}

// -------------------------------------------------------------
// Step 4 & Step 7: 404 Guardian Middleware
// -------------------------------------------------------------
GuardianMiddleware::handle404($path, "The requested route '$path' does not exist.");

