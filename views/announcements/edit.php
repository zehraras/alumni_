<div class="card">
    <span class="badge">📢 Duyuru Düzenle</span>
    <h1>Düzenle: #<?= htmlspecialchars($item['id']) ?></h1>
    <form action="<?= htmlspecialchars($prefix) ?>/announcements/<?= $item['id'] ?>/update" method="POST" style="margin-top:2rem;">
        <div class="form-group">
            <label>Başlık</label>
            <input type="text" name="title" value="<?= htmlspecialchars($item['title'] ?? $item['başlık'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>İçerik</label>
            <textarea name="content" rows="5" style="width:100%; padding:0.75rem; border:1px solid var(--border); border-radius:6px;" required><?= htmlspecialchars($item['content'] ?? $item['içerik'] ?? '') ?></textarea>
        </div>
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn" style="background:#eab308;">Güncelle</button>
            <a href="<?= htmlspecialchars($prefix) ?>/announcements/<?= $item['id'] ?>" class="btn" style="background:var(--text-muted);">İptal</a>
        </div>
    </form>
</div>
