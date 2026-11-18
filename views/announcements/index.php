<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <span class="badge" style="background: #fef3c7; color: #92400e;">📢 Haberler & Duyurular</span>
            <h1 style="font-size: 1.75rem; font-weight: 800; margin-top: 0.25rem;">Bölüm & Etkinlik Duyuruları</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Mezunlar derneği, kariyer etkinlikleri ve üniversite bültenleri.</p>
        </div>
        <div>
            <a href="<?= htmlspecialchars($prefix) ?>/announcements/create" class="btn" style="background: #16a34a;">
                + Yeni Duyuru Ekle
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="user-table">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Duyuru Başlığı</th>
                    <th>Yayın Tarihi</th>
                    <th style="text-align: right;">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 3rem; color:var(--text-muted);">
                            Henüz yayınlanmış bir duyuru bulunmamaktadır.
                        </td>
                    </tr>
                <?php else: foreach ($items as $item): 
                    $annId = htmlspecialchars($item['id'] ?? '');
                    $title = htmlspecialchars($item['title'] ?? $item['başlık'] ?? 'Başlıksız Duyuru');
                    $date = htmlspecialchars($item['createdAt'] ?? '-');
                ?>
                    <tr>
                        <td><span style="font-weight: 700; color: var(--text-muted); font-size: 0.85rem;">#<?= $annId ?></span></td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-main); font-size: 0.95rem;">
                                📢 <?= $title ?>
                            </div>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.88rem;">
                            📅 <?= $date ?>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 0.35rem;">
                                <a href="<?= htmlspecialchars($prefix) ?>/announcements/<?= $annId ?>" class="btn" style="padding: 0.35rem 0.75rem; font-size: 0.82rem; background: #0284c7;">Görüntüle</a>
                                <a href="<?= htmlspecialchars($prefix) ?>/announcements/<?= $annId ?>/edit" class="btn" style="padding: 0.35rem 0.75rem; font-size: 0.82rem; background: #eab308;">Düzenle</a>
                                <form action="<?= htmlspecialchars($prefix) ?>/announcements/<?= $annId ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Bu duyuruyu silmek istediğinize emin misiniz?');">
                                    <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.82rem;">Sil</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
