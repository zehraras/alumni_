<div class="card">
    <span class="badge">📢 Yeni Duyuru</span>
    <h1>Duyuru Ekle</h1>
    <form action="<?= htmlspecialchars($prefix) ?>/announcements" method="POST" style="margin-top:2rem;">
        <div class="form-group">
            <label>Başlık</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>İçerik</label>
            <textarea name="content" rows="5" style="width:100%; padding:0.75rem; border:1px solid var(--border); border-radius:6px;" required></textarea>
        </div>
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn" style="background:#10b981;">Kaydet</button>
            <a href="<?= htmlspecialchars($prefix) ?>/announcements" class="btn" style="background:var(--text-muted);">İptal</a>
        </div>
    </form>
</div>
