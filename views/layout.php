<?php
// Layout view
$homeUrl = $prefix . '/';
$usersUrl = $prefix . '/users';
$aboutUrl = $prefix . '/about';

$homeActive = $activePage === 'home' ? 'class="active"' : '';
$usersActive = $activePage === 'users' ? 'class="active"' : '';
$aboutActive = $activePage === 'about' ? 'class="active"' : '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Alumni System</title>
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
            --danger: #ef4444;
            --danger-hover: #dc2626;
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
        .logo { font-size: 1.25rem; font-weight: 700; color: var(--primary); text-decoration: none; }
        nav { display: flex; gap: 1rem; }
        nav a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            transition: all 0.2s;
        }
        nav a:hover, nav a.active {
            color: var(--primary);
            background-color: #eff6ff;
        }
        main {
            flex: 1;
            padding: 2rem;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        .badge {
            display: inline-block;
            background: var(--badge-bg);
            color: var(--badge-text);
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            border: none;
        }
        .btn:hover { background: var(--primary-hover); }
        .btn-danger {
            background: var(--danger);
        }
        .btn-danger:hover {
            background: var(--danger-hover);
        }
        
        table.user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        table.user-table th, table.user-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        table.user-table th {
            background: #f8fafc;
            font-weight: 600;
            color: var(--text-muted);
        }
        
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 1rem;
        }
        .flex-gap {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <header>
        <a href="<?= htmlspecialchars($homeUrl) ?>" class="logo">🎓 AlumniSystem</a>
        <nav>
            <a href="<?= htmlspecialchars($homeUrl) ?>" <?= $homeActive ?>>Ana Sayfa</a>
            <a href="<?= htmlspecialchars($usersUrl) ?>" <?= $usersActive ?>>Kullanıcılar</a>
            <a href="<?= htmlspecialchars($aboutUrl) ?>" <?= $aboutActive ?>>Hakkında</a>
        </nav>
    </header>
    <main>
        <?= $content ?>
    </main>
</body>
</html>
