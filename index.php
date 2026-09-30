<?php

// Alumni Management System - Week 02 & Week 03
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

// Helper: Get users from data/users.json
function getUsersList() {
    $file = __DIR__ . '/data/users.json';
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

// Helper: Save new user to data/users.json
function saveNewUser($user) {
    $file = __DIR__ . '/data/users.json';
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $users = getUsersList();
    array_unshift($users, $user); // En yeni kullanıcı en başta
    file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    return $user;
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
// GET /users -> Web Interface to View and Add Users
// -------------------------------------------------------------
if ($path === '/users') {
    $users = getUsersList();
    
    // Build Table Rows
    $tableRows = '';
    if (empty($users)) {
        $tableRows = '<tr><td colspan="6" style="text-align:center; color: var(--text-muted); padding: 2rem;">Henüz kayıtlı kullanıcı bulunmamaktadır.</td></tr>';
    } else {
        foreach ($users as $u) {
            $id = htmlspecialchars($u['id'] ?? '-');
            $isim = htmlspecialchars($u['İsim'] ?? $u['name'] ?? $u['isim'] ?? 'İsimsiz');
            $soyisim = htmlspecialchars($u['Soyisim'] ?? $u['surname'] ?? '');
            $tamAd = trim($isim . ' ' . $soyisim);
            $bolum = htmlspecialchars($u['Bölüm'] ?? $u['department'] ?? $u['bolum'] ?? '-');
            $yil = htmlspecialchars($u['MezuniyetYılı'] ?? $u['graduationYear'] ?? $u['mezuniyet'] ?? '-');
            $sehir = htmlspecialchars($u['Şehir'] ?? $u['city'] ?? $u['email'] ?? '-');

            $tableRows .= "<tr>
                <td><a href=\"{$apiUrl}/{$id}\" target=\"_blank\" title=\"Tekil JSON gör (Adım 4b)\" style=\"color: var(--primary); text-decoration: none; font-weight: bold;\">#{$id} ↗</a></td>
                <td><strong>{$tamAd}</strong></td>
                <td>{$bolum}</td>
                <td><span class=\"badge badge-blue\" style=\"margin:0;\">{$yil}</span></td>
                <td>{$sehir}</td>
                <td><button onclick=\"deleteUser('{$id}')\" style=\"background: #ef4444; color: white; border: none; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;\">Sil 🗑️</button></td>
            </tr>";
        }
    }

    $apiUrl = $prefix . '/api/users';
    header('Content-Type: text/html; charset=utf-8');
    $content = <<<HTML
        <div class="card">
            <span class="badge">🌐 Web Arayüzü (URL/users)</span>
            <h1>🎓 Kayıtlı Mezunlar & Kullanıcılar</h1>
            <p>Hocanızın tahtaya yazdığı gibi: <strong>URL/users</strong> insanların gördüğü web arayüzüdür; <strong>URL/api/users</strong> ise Postman'in konuştuğu JSON API'dir. Postman'den eklediğiniz tüm veriler bu tabloda anında görünür!</p>

            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID (Adım 4b)</th>
                        <th>İsim / Soyisim</th>
                        <th>Bölüm</th>
                        <th>Mezuniyet Yılı</th>
                        <th>Şehir / İletişim</th>
                        <th>İşlem (Adım 6)</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    {$tableRows}
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>➕ Arayüzden Yeni Mezun Ekle</h3>
            <p>Dilerseniz Postman yerine doğrudan buradan da form doldurarak <code>POST /api/users</code> servisini çalıştırabilirsiniz:</p>
            
            <form id="addUserForm" onsubmit="submitUser(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label for="isimInput">İsim</label>
                        <input type="text" id="isimInput" name="İsim" placeholder="Örn: Zehra" required>
                    </div>
                    <div class="form-group">
                        <label for="soyisimInput">Soyisim</label>
                        <input type="text" id="soyisimInput" name="Soyisim" placeholder="Örn: Aras">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="bolumInput">Bölüm</label>
                        <input type="text" id="bolumInput" name="Bölüm" placeholder="Örn: Bilgisayar Mühendisliği">
                    </div>
                    <div class="form-group">
                        <label for="yilInput">Mezuniyet Yılı</label>
                        <input type="number" id="yilInput" name="MezuniyetYılı" placeholder="Örn: 2024" value="2024">
                    </div>
                    <div class="form-group">
                        <label for="sehirInput">Şehir</label>
                        <input type="text" id="sehirInput" name="Şehir" placeholder="Örn: İstanbul">
                    </div>
                </div>
                <button type="submit" class="btn">🚀 Kullanıcıyı Ekle (POST /api/users)</button>
            </form>

            <div id="resultBox" style="display:none; margin-top: 1.5rem;">
                <h4>✅ Sunucudan Dönen Canlı JSON Yanıtı:</h4>
                <pre class="json-preview" id="jsonResult"></pre>
            </div>
        </div>

        <script>
            async function submitUser(e) {
                e.preventDefault();
                const form = document.getElementById('addUserForm');
                const formData = new FormData(form);
                
                try {
                    const response = await fetch('{$apiUrl}', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    document.getElementById('resultBox').style.display = 'block';
                    document.getElementById('jsonResult').innerText = JSON.stringify(data, null, 2);
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } catch (err) {
                    alert('Hata: ' + err.message);
                }
            }

            async function deleteUser(id) {
                if (!confirm('ID #' + id + ' kullanıcısını silmek istediğinize emin misiniz? (Adım 6: DELETE /api/users/' + id + ')')) return;
                try {
                    const res = await fetch('{$apiUrl}/' + id, { method: 'DELETE' });
                    const data = await res.json();
                    alert(data.message || 'Silindi');
                    window.location.reload();
                } catch (err) {
                    alert('Hata: ' + err.message);
                }
            }
        </script>
HTML;
    echo renderLayout("Kullanıcılar", $content, $prefix, 'users');
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
// Week 03 - Step 3: POST & GET /api/users -> JSON
// -------------------------------------------------------------
if ($path === '/api/users') {
    header('Content-Type: application/json; charset=utf-8');

    if ($method === 'POST') {
        $data = parseRequestBody();

        if (!$data || !is_array($data) || empty($data)) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Bad Request',
                'message' => 'Lütfen geçerli veriler gönderin.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Dynamic Alumni fields: Include id, timestamp and all custom fields ("what you send comes back")
        $createdUser = [
            'id' => rand(100, 999),
            'createdAt' => date('c')
        ];

        foreach ($data as $key => $value) {
            $createdUser[$key] = $value;
        }

        // Save to persistent file storage
        saveNewUser($createdUser);

        http_response_code(201); // 201 Created
        echo json_encode($createdUser, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($method === 'GET') {
        // Return all registered users in JSON format
        $users = getUsersList();
        echo json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// -------------------------------------------------------------
// Week 03 - Step 5: PUT / PATCH /api/users/{id} -> Update user
// -------------------------------------------------------------
if (preg_match('#^/api/users/([^/]+)$#', $path, $matches)) {
    header('Content-Type: application/json; charset=utf-8');
    $id = $matches[1];

    // PUT or PATCH: Update user
    if ($method === 'PUT' || $method === 'PATCH') {
        $data = parseRequestBody();

        if (!$data || !is_array($data) || empty($data)) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Bad Request',
                'message' => 'Lütfen güncellenecek alanları JSON veya form verisi olarak gönderin.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }

        $users = getUsersList();
        $userIndex = -1;

        foreach ($users as $index => $u) {
            if (isset($u['id']) && (string)$u['id'] === (string)$id) {
                $userIndex = $index;
                break;
            }
        }

        if ($userIndex === -1) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Not Found',
                'message' => "ID {$id} olan kullanıcı bulunamadı."
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Update user fields
        foreach ($data as $k => $v) {
            if ($k === 'id') continue; // ID değiştirilmez
            $users[$userIndex][$k] = $v;
        }
        $users[$userIndex]['updatedAt'] = date('c');

        // Save to file
        $file = __DIR__ . '/data/users.json';
        file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        http_response_code(200);
        echo json_encode($users[$userIndex], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // GET single user: /api/users/{id}
    if ($method === 'GET') {
        $users = getUsersList();
        foreach ($users as $u) {
            if (isset($u['id']) && (string)$u['id'] === (string)$id) {
                http_response_code(200);
                echo json_encode($u, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        http_response_code(404);
        echo json_encode([
            'error' => 'Not Found',
            'message' => "ID {$id} olan kullanıcı bulunamadı."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Week 03 - Step 6: DELETE /api/users/{id} -> Delete user
    if ($method === 'DELETE') {
        $users = getUsersList();
        $userIndex = -1;
        $deletedUser = null;

        foreach ($users as $index => $u) {
            if (isset($u['id']) && (string)$u['id'] === (string)$id) {
                $userIndex = $index;
                $deletedUser = $u;
                break;
            }
        }

        if ($userIndex === -1) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Not Found',
                'message' => "ID {$id} olan kullanıcı bulunamadı."
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Kullanıcıyı listeden kaldır
        array_splice($users, $userIndex, 1);

        // users.json dosyasına kaydet
        $file = __DIR__ . '/data/users.json';
        file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        http_response_code(200);
        echo json_encode([
            'message' => "ID {$id} olan kullanıcı başarıyla silindi.",
            'deletedId' => $id,
            'user' => $deletedUser
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
