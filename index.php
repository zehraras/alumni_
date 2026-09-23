<?php

// Alumni Management System - Week 02 Routing
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

// Method check
if ($method !== 'GET') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Method Not Allowed";
    exit;
}

// Base styling for browser views
function renderLayout($title, $content, $prefix = '') {
    $homeUrl = $prefix . '/';
    $aboutUrl = $prefix . '/about';
    $helloUrl = $prefix . '/hello';
    $helloEmreUrl = $prefix . '/hello/emre';
    $sumUrl = $prefix . '/sum/15/27';

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
        }
        nav a:hover, nav a.active {
            color: var(--primary);
        }
        main {
            flex: 1;
            max-width: 900px;
            width: 100%;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }
        p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }
        .badge {
            display: inline-block;
            background: var(--badge-bg);
            color: var(--badge-text);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .routes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
        footer {
            border-top: 1px solid var(--border);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
            background: var(--card-bg);
        }
    </style>
</head>
<body>
    <header>
        <a href="{$homeUrl}" class="logo">🎓 Alumni System</a>
        <nav>
            <a href="{$homeUrl}">Home</a>
            <a href="{$aboutUrl}">About</a>
            <a href="{$helloUrl}">Hello</a>
        </nav>
    </header>
    <main>
        {$content}
    </main>
    <footer>
        &copy; 2026 Alumni Tracking System &bull; Powered by PHP 8.2 & Docker
    </footer>
</body>
</html>
HTML;
}

// -------------------------------------------------------------
// Step 1 & 5: GET / -> temporary main page (with ok status)
// -------------------------------------------------------------
if ($path === '/') {
    // If called via curl/cli or explicitly requesting text, support returning "ok"
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (isset($_GET['raw']) || str_contains($accept, 'text/plain')) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "ok";
        exit;
    }

    header('Content-Type: text/html; charset=utf-8');
    $content = <<<HTML
        <div class="card">
            <span class="badge">● Status: ok</span>
            <h1>🎓 Alumni Management System</h1>
            <p>Welcome to the Alumni Tracking System. This is the temporary main page for Week 02 development.</p>
            <hr style="border: none; border-top: 1px solid var(--border); margin: 1.5rem 0;">
            <h3>Active Project Routes</h3>
            <p>You can click on the routes below to test them directly in your browser:</p>
            <div class="routes-grid">
                <div class="route-item">
                    <span class="route-method">GET</span>
                    <a href="{$prefix}/" class="route-path">/</a>
                    <span class="route-desc">Temporary main page (Status: ok)</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET</span>
                    <a href="{$prefix}/about" class="route-path">/about</a>
                    <span class="route-desc">Temporary about page</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET</span>
                    <a href="{$prefix}/hello" class="route-path">/hello</a>
                    <span class="route-desc">Returns "Hello, World!"</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET</span>
                    <a href="{$prefix}/hello/emre" class="route-path">/hello/emre</a>
                    <span class="route-desc">Returns "Hello, Emre!"</span>
                </div>
                <div class="route-item">
                    <span class="route-method">GET</span>
                    <a href="{$prefix}/sum/15/27" class="route-path">/sum/15/27</a>
                    <span class="route-desc">Returns sum of two numbers (42)</span>
                </div>
            </div>
        </div>
HTML;
    echo renderLayout("Home", $content, $prefix);
    exit;
}

// -------------------------------------------------------------
// Step 6: GET /about -> temporary about page
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
                <li><strong>Milestone:</strong> Week 02 - Routing & First Routes</li>
            </ul>

            <div style="margin-top: 2rem;">
                <a href="{$prefix}/" style="display: inline-block; background: var(--primary); color: white; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 500;">&larr; Back to Main Page</a>
            </div>
        </div>
HTML;
    echo renderLayout("About", $content, $prefix);
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
