<div class="card" style="max-width: 680px; margin: 0 auto;">
    <a href="<?= htmlspecialchars($prefix) ?>/announcements" style="color: var(--text-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        &larr; Tüm Duyurulara Dön
    </a>

    <div style="margin: 1rem 0 1.5rem;">
        <span class="badge" style="background: #fef3c7; color: #92400e;">📢 Yeni Yayın</span>
        <h1 style="font-size: 1.75rem; font-weight: 800; margin-top: 0.35rem;">Yeni Bölüm Duyurusu Ekle</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Mezunlara, öğrencilere ve üniversite topluluğuna duyuru yayınlayın.</p>
    </div>

    <form action="<?= htmlspecialchars($prefix) ?>/announcements" method="POST">
        <div class="form-group">
            <label for="titleInput">Duyuru Başlığı *</label>
            <input type="text" id="titleInput" name="title" placeholder="Örn: 2026 Mezunlar Buluşması ve Gala Gecesi" required>
        </div>
        <div class="form-group">
            <label for="contentInput">Duyuru Metni / İçeriği *</label>
            <textarea id="contentInput" name="content" rows="6" placeholder="Etkinlik tarihi, saati, yeri ve detayları hakkında açıklama yazın..." required></textarea>
        </div>
        <div style="margin-top: 2rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
            <a href="<?= htmlspecialchars($prefix) ?>/announcements" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn" style="background: #16a34a; padding: 0.65rem 1.75rem;">
                📢 Duyuruyu Yayınla
            </button>
        </div>
    </form>
</div>
