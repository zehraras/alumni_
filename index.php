<?php

// Alumni Management System - Week 02 & Week 03 & Week 04
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/ApiUserController.php';

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

// Base styling for browser views
function renderLayout($title, $content, $prefix = '', $activePage = '') {
    $homeUrl = $prefix . '/';
    $usersUrl = $prefix . '/users';
    $aboutUrl = $prefix . '/about';
    $healthUrl = $prefix . '/api/health';

    $homeActive = $activePage === 'home' ? 'class="active"' : '';
    $usersActive = $activePage === 'users' ? 'class="active"' : '';
    $aboutActive = $activePage === 'about' ? 'class="active"' : '';

    return <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} | Alumni System</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --badge-bg: #dcfce7;
            --badge-text: #15803d;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        nav {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        nav a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            transition: color 0.2s;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }
        nav a:hover, nav a.active {
            color: var(--primary);
            background: #eff6ff;
        }
        main {
            flex: 1;
            max-width: 950px;
            width: 100%;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        h1 {
            font-size: 1.85rem;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }
        p {
            color: var(--text-muted);
            margin-bottom: 1.25rem;
        }
        .badge {
            display: inline-block;
            background: var(--badge-bg);
            color: var(--badge-text);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .badge-blue {
            background: #dbeafe;
            color: #1e40af;
        }
        .routes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .route-item {
            background: #f1f5f9;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .route-method {
            font-size: 0.75rem;
            font-weight: bold;
            color: var(--primary);
            text-transform: uppercase;
        }
        .route-path {
            font-family: monospace;
            font-size: 0.95rem;
            margin: 0.25rem 0;
            display: block;
            color: #0f172a;
            text-decoration: none;
        }
        .route-path:hover {
            text-decoration: underline;
        }
        .route-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        /* Form & Table styles */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .form-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
        }
        .form-group input {
            padding: 0.6rem 0.8rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            border-color: var(--primary);
        }
        .btn {
            background: var(--primary);
            color: white;
            padding: 0.7rem 1.4rem;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover {
            background: var(--primary-hover);
        }
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .user-table th, .user-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }
        .user-table th {
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
        }
        .user-table tr:hover {
            background: #f1f5f9;
        }
        .json-preview {
            background: #0f172a;
            color: #38bdf8;
            padding: 1rem;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.85rem;
            overflow-x: auto;
            margin-top: 1rem;
        }
        footer {
            border-top: 1px solid var(--border);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
            background: var(--card-bg);
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header>
        <a href="{$homeUrl}" class="logo">🎓 Alumni System</a>
        <nav>
            <a href="{$homeUrl}" {$homeActive}>Home</a>
            <a href="{$usersUrl}" {$usersActive}>Users (Arayüz)</a>
            <a href="{$aboutUrl}" {$aboutActive}>About</a>
            <a href="{$healthUrl}" target="_blank">API Health ↗</a>
        </nav>
    </header>
    <main>
        {$content}
    </main>
    <footer>
        &copy; 2026 Alumni Tracking System &bull; Web Programming Week 03 &bull; Powered by PHP & Docker
    </footer>
</body>
</html>
HTML;
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

    $users = getUsersList();
    $userCount = count($users);

    header('Content-Type: text/html; charset=utf-8');
    $content = <<<HTML
        <div class="card">
            <span class="badge">● Status: ok</span>
            <span class="badge badge-blue">👥 Kayıtlı Mezun: {$userCount}</span>
            <h1>🎓 Alumni Management System</h1>
            <p>Mezun Yönetim ve Takip Sistemi geliştirme ortamına hoş geldiniz. Aşağıdaki kartlardan arayüzü ve API servislerini test edebilirsiniz.</p>
            
            <div style="margin-top: 1.5rem; padding: 1.25rem; background: #eff6ff; border-radius: 8px; border: 1px solid #bfdbfe;">
                <h3 style="color: #1e40af; margin-bottom: 0.5rem;">🌟 Yeni Özellik: Arayüzden Kullanıcıları Görün ve Ekleyin!</h3>
                <p style="color: #1e3a8a; margin-bottom: 1rem;">Postman'den veya tarayıcıdan eklenen tüm mezunları doğrudan web sayfasında görebilirsiniz.</p>
                <a href="{$prefix}/users" class="btn" style="text-decoration: none; display: inline-block;">👉 Users Sayfasına Git</a>
            </div>

            <hr style="border: none; border-top: 1px solid var(--border); margin: 2rem 0;">
            
            <h3>Aktif Sistem Rotaları</h3>
            <div class="routes-grid">
                <div class="route-item">
                    <span class="route-method">GET (UI)</span>
                    <a href="{$prefix}/users" class="route-path">/users</a>
                    <span class="route-desc">Mezun listesi ve kullanıcı ekleme arayüzü</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET (API)</span>
                    <a href="{$prefix}/api/health" class="route-path">/api/health</a>
                    <span class="route-desc">JSON durum kontrolü {"status": "ok"}</span>
                </div>
                <div class="route-item" style="border-left: 3px solid #f59e0b;">
                    <span class="route-method" style="background: #f59e0b; color: white;">GET (DOCS)</span>
                    <a href="{$prefix}/api/swagger" class="route-path" style="color: #d97706;">/api/swagger</a>
                    <span class="route-desc"><strong>YENİ:</strong> Otomatik API Dokümantasyonu (Swagger)</span>
                </div>
                <div class="route-item">
                    <span class="route-method">POST (API)</span>
                    <a href="{$prefix}/api/users" class="route-path">/api/users</a>
                    <span class="route-desc">Postman / Form ile mezun kaydetme</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET (API)</span>
                    <a href="{$prefix}/api/users" class="route-path">/api/users</a>
                    <span class="route-desc">Tüm kullanıcıları JSON olarak alma</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET</span>
                    <a href="{$prefix}/about" class="route-path">/about</a>
                    <span class="route-desc">Geçici hakkında sayfası</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET</span>
                    <a href="{$prefix}/hello/emre" class="route-path">/hello/emre</a>
                    <span class="route-desc">Selamlama rotası</span>
                </div>
            </div>
        </div>
HTML;
    echo renderLayout("Ana Sayfa", $content, $prefix, 'home');
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
// Week 02 - Step 6: GET /about -> Temporary about page
// -------------------------------------------------------------
if ($path === '/about') {
    header('Content-Type: text/html; charset=utf-8');
    $content = <<<HTML
        <div class="card">
            <span class="badge">ℹ️ Temporary About Page</span>
            <h1>About Alumni System</h1>
            <p>The Alumni Management System is designed to track graduates, organize university events, and maintain strong connections between alumni and their alma mater.</p>
            
            <hr style="border: none; border-top: 1px solid var(--border); margin: 1.5rem 0;">
            
            <h3 style="margin-bottom: 0.75rem;">Project Specifications</h3>
            <ul style="margin-left: 1.5rem; color: var(--text-muted); line-height: 2;">
                <li><strong>Backend:</strong> PHP 8.2 (CLI Built-in Web Server)</li>
                <li><strong>Database:</strong> PostgreSQL 15</li>
                <li><strong>Deployment:</strong> Single-command Docker Compose (<code>docker compose up</code>)</li>
                <li><strong>AI Assistant:</strong> Antigravity (Google DeepMind)</li>
                <li><strong>Milestone:</strong> Week 03 - API & Endpoints (POST /api/users)</li>
            </ul>

            <div style="margin-top: 2rem;">
                <a href="{$prefix}/" style="display: inline-block; background: var(--primary); color: white; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 500;">&larr; Back to Main Page</a>
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
// 404 Fallback
// -------------------------------------------------------------
http_response_code(404);
header('Content-Type: text/html; charset=utf-8');
$notFoundContent = <<<HTML
    <div class="card" style="text-align: center;">
        <h1 style="color: #ef4444;">404 Not Found</h1>
        <p>The requested route does not exist.</p>
        <a href="{$prefix}/" style="color: var(--primary); text-decoration: none; font-weight: 500;">&larr; Return to Home</a>
    </div>
HTML;
echo renderLayout("404 Not Found", $notFoundContent, $prefix);
