<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Sayfa Bulunamadı | Alumni System</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        .error-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 3rem 2rem;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .error-code {
            font-size: 5rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .badge {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }
        h2 {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }
        p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        .reason-box {
            background: #f1f5f9;
            border-left: 4px solid var(--primary);
            padding: 0.75rem 1rem;
            border-radius: 4px;
            text-align: left;
            font-size: 0.9rem;
            color: #334155;
            margin-bottom: 1.5rem;
            word-break: break-all;
        }
        .btn-group {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: var(--primary-hover);
        }
        .btn-secondary {
            background: #e2e8f0;
            color: var(--text-main);
        }
        .btn-secondary:hover {
            background: #cbd5e1;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">404</div>
        <span class="badge">🛡️ 404 Guardian Middleware</span>
        <h2>Aradığınız Sayfa Bulunamadı</h2>
        <p>İstediğiniz kaynak veya rota sistemde mevcut değil ya da taşınmış olabilir.</p>

        <div class="reason-box">
            <strong>İstenen Rota:</strong> <code><?= htmlspecialchars($path) ?></code><br>
            <strong>Açıklama:</strong> <?= htmlspecialchars($reason) ?>
        </div>

        <div class="btn-group">
            <a href="/" class="btn btn-primary">Ana Sayfaya Git</a>
            <a href="/users" class="btn btn-secondary">Mezunlar</a>
            <a href="/job-postings" class="btn btn-secondary">İş İlanları</a>
            <a href="javascript:history.back()" class="btn btn-secondary">&larr; Geri Dön</a>
        </div>
    </div>
</body>
</html>
