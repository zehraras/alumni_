<?php
$annId = htmlspecialchars($item['id'] ?? '');
$title = htmlspecialchars($item['title'] ?? $item['başlık'] ?? 'Başlıksız Duyuru');
$date = htmlspecialchars($item['createdAt'] ?? '-');
$content = htmlspecialchars($item['content'] ?? $item['içerik'] ?? 'İçerik belirtilmemiş.');
?>
<div class="card">
    <a href="<?= htmlspecialchars($prefix) ?>/announcements" style="color: var(--text-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        &larr; Tüm Duyurulara Dön
    </a>

    <div style="margin: 1.25rem 0 1.75rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
        <span class="badge" style="background: #fef3c7; color: #92400e;">📢 Bölüm Duyurusu #<?= $annId ?></span>
        <h1 style="font-size: 2rem; font-weight: 800; margin: 0.5rem 0 0.25rem;"><?= $title ?></h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">📅 Yayın Tarihi: <?= $date ?></p>
    </div>

    <div style="padding: 1.75rem; background: #f8fafc; border: 1px solid var(--border); border-radius: 12px; font-size: 1.05rem; line-height: 1.7; white-space: pre-wrap; color: var(--text-main);">
<?= $content ?>
    </div>

    <div style="margin-top: 2rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= htmlspecialchars($prefix) ?>/announcements/<?= $annId ?>/edit" class="btn" style="background: #eab308;">
            ✏️ Duyuruyu Düzenle
        </a>
        <form action="<?= htmlspecialchars($prefix) ?>/announcements/<?= $annId ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Bu duyuruyu silmek istediğinize emin misiniz?');">
            <button type="submit" class="btn btn-danger">🗑️ Sil</button>
        </form>
    </div>
</div>
