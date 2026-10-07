<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
        <div>
            <span class="badge" style="background:#fef08a; color:#854d0e;">📢 Duyuru Detayı</span>
            <h1 style="margin-top:0.5rem;"><?= htmlspecialchars($item['title'] ?? $item['başlık'] ?? 'Başlıksız') ?></h1>
            <small style="color:var(--text-muted);">Tarih: <?= htmlspecialchars($item['createdAt'] ?? '-') ?></small>
        </div>
        <a href="<?= htmlspecialchars($prefix) ?>/announcements" class="btn" style="background:var(--text-muted);">&larr; Geri</a>
    </div>
    <div style="padding: 1.5rem; background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; white-space: pre-wrap;">
<?= htmlspecialchars($item['content'] ?? $item['içerik'] ?? 'İçerik yok.') ?>
    </div>
    <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
        <a href="<?= htmlspecialchars($prefix) ?>/announcements/<?= $item['id'] ?>/edit" class="btn" style="background: #eab308;">Düzenle</a>
    </div>
</div>
