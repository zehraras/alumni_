<div class="card">
    <span class="badge" style="background:#fef08a; color:#854d0e;">📢 Duyurular (Web Arayüzü)</span>
    <h1>Bölüm Duyuruları</h1>
    
    <div style="margin-bottom: 1rem; text-align: right;">
        <a href="<?= htmlspecialchars($prefix) ?>/announcements/create" class="btn" style="background:#10b981;">+ Yeni Duyuru Ekle</a>
    </div>

    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Başlık</th>
                <th>Tarih</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="4" style="text-align:center; padding: 2rem; color:var(--text-muted);">Henüz duyuru yok.</td></tr>
            <?php else: foreach ($items as $item): ?>
                <tr>
                    <td>#<?= htmlspecialchars($item['id'] ?? '') ?></td>
                    <td><strong><?= htmlspecialchars($item['title'] ?? $item['başlık'] ?? 'Başlıksız') ?></strong></td>
                    <td><?= htmlspecialchars($item['createdAt'] ?? '-') ?></td>
                    <td>
                        <a href="<?= htmlspecialchars($prefix) ?>/announcements/<?= $item['id'] ?>" class="btn" style="padding: 0.3rem 0.6rem; font-size: 0.85rem; background: #0284c7;">Görüntüle</a>
                        <a href="<?= htmlspecialchars($prefix) ?>/announcements/<?= $item['id'] ?>/edit" class="btn" style="padding: 0.3rem 0.6rem; font-size: 0.85rem; background: #eab308;">Düzenle</a>
                        <form action="<?= htmlspecialchars($prefix) ?>/announcements/<?= $item['id'] ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                            <button type="submit" class="btn btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.85rem;">Sil</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
