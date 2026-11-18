<?php
// Global Layout View
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
            --primary-light: #eff6ff;
            --accent: #0284c7;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --border-hover: #cbd5e1;
            --badge-bg: #dcfce7;
            --badge-text: #15803d;
            --danger: #dc2626;
            --danger-hover: #b91c1c;
            --success: #16a34a;
            --warning: #eab308;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Sticky Header with Blur */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background-color: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
        }

        .logo {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            letter-spacing: -0.5px;
        }
        .logo span {
            color: var(--primary);
        }

        nav {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        nav a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.92rem;
            padding: 0.5rem 0.85rem;
            border-radius: var(--radius-sm);
            transition: all 0.15s ease-in-out;
        }

        nav a:hover {
            color: var(--primary);
            background-color: var(--primary-light);
        }

        nav a.active {
            color: var(--primary);
            background-color: var(--primary-light);
            font-weight: 600;
        }

        main {
            flex: 1;
            padding: 2.25rem 2rem;
            max-width: 1080px;
            margin: 0 auto;
            width: 100%;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            border-color: var(--border-hover);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: var(--badge-bg);
            color: var(--badge-text);
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            background: var(--primary);
            color: white;
            padding: 0.65rem 1.25rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-danger {
            background: var(--danger);
        }
        .btn-danger:hover {
            background: var(--danger-hover);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            box-shadow: none;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
            color: var(--text-main);
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            margin-top: 1rem;
            background: #fff;
        }

        table.user-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        table.user-table th {
            background: #f8fafc;
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.85rem 1.15rem;
            border-bottom: 1px solid var(--border);
        }

        table.user-table td {
            padding: 1rem 1.15rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.93rem;
            vertical-align: middle;
        }

        table.user-table tbody tr:last-child td {
            border-bottom: none;
        }

        table.user-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.88rem;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--text-main);
            background-color: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .flex-gap {
            display: flex;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        /* Footer */
        footer {
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding: 1.5rem 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: auto;
        }

        /* Responsive Breakpoint */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 0.75rem;
                padding: 1rem;
            }
            nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            main {
                padding: 1.25rem 1rem;
            }
            .card {
                padding: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="<?= htmlspecialchars($homeUrl) ?>" class="logo">
            🎓 Alumni<span>System</span>
        </a>
        <nav>
            <a href="<?= htmlspecialchars($homeUrl) ?>" <?= $homeActive ?>>Ana Sayfa</a>
            <a href="<?= htmlspecialchars($usersUrl) ?>" <?= $usersActive ?>>Mezunlar</a>
            <a href="<?= htmlspecialchars($prefix) ?>/announcements" <?= ($activePage === 'announcements') ? 'class="active"' : '' ?>>Duyurular</a>
            <a href="<?= htmlspecialchars($prefix) ?>/job-postings" <?= ($activePage === 'job_postings') ? 'class="active"' : '' ?>>İş İlanları</a>
            <a href="<?= htmlspecialchars($prefix) ?>/applications" <?= ($activePage === 'applications') ? 'class="active"' : '' ?>>Başvurular</a>
            <a href="<?= htmlspecialchars($aboutUrl) ?>" <?= $aboutActive ?>>Hakkında</a>
        </nav>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Alumni Management System &bull; Üniversite Mezun Takip & Kariyer Platformu</p>
    </footer>
</body>
</html>
