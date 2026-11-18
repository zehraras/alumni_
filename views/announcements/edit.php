<?php
$annId = htmlspecialchars($item['id'] ?? '');
$title = htmlspecialchars($item['title'] ?? $item['başlık'] ?? '');
$content = htmlspecialchars($item['content'] ?? $item['içerik'] ?? '');
?>
<div class="card" style="max-width: 680px; margin: 0 auto;">
    <a href="<?= htmlspecialchars($prefix) ?>/announcements/<?= $annId ?>" style="color: var(--text-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        &larr; Duyuru Detayına Dön
    </a>

    <div style="margin: 1rem 0 1.5rem;">
        <span class="badge" style="background: #fef9c3; color: #854d0e;">✏️ Duyuru Düzenleme</span>
        <h1 style="font-size: 1.75rem; font-weight: 800; margin-top: 0.35rem;">Duyuruyu Düzenle #<?= $annId ?></h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Duyuru başlığını veya içeriğini güncelleyin.</p>
    </div>

    <form action="<?= htmlspecialchars($prefix) ?>/announcements/<?= $annId ?>/update" method="POST">
        <div class="form-group">
            <label for="titleInput">Duyuru Başlığı *</label>
            <input type="text" id="titleInput" name="title" value="<?= $title ?>" required>
        </div>
        <div class="form-group">
            <label for="contentInput">Duyuru Metni / İçeriği *</label>
            <textarea id="contentInput" name="content" rows="6" required><?= $content ?></textarea>
        </div>
        <div style="margin-top: 2rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
            <a href="<?= htmlspecialchars($prefix) ?>/announcements/<?= $annId ?>" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn" style="background: #eab308; padding: 0.65rem 1.75rem;">
                💾 Değişiklikleri Kaydet
            </button>
        </div>
    </form>
</div>
