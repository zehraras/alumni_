<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <span class="badge" style="background: #eff6ff; color: #1e40af;">👥 Mezun Rehberi</span>
            <h1 style="font-size: 1.75rem; font-weight: 800; margin-top: 0.25rem;">Kayıtlı Mezunlar</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Sistemde kayıtlı üniversite mezunlarının iletişim ve kariyer profilleri.</p>
        </div>
        <div>
            <a href="<?= htmlspecialchars($prefix) ?>/users/create" class="btn" style="background: #16a34a;">
                + Yeni Mezun Ekle
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="user-table">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Mezun Adı & Pozisyon</th>
                    <th>Bölüm</th>
                    <th>Mezuniyet</th>
                    <th>Şehir</th>
                    <th style="text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color: var(--text-muted); padding: 3rem;">
                            Henüz kayıtlı mezun bulunmamaktadır.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): 
                        $id = htmlspecialchars($u['id'] ?? '-');
                        $isim = htmlspecialchars($u['İsim'] ?? $u['name'] ?? $u['isim'] ?? 'İsimsiz');
                        $soyisim = htmlspecialchars($u['Soyisim'] ?? $u['surname'] ?? '');
                        $tamAd = trim($isim . ' ' . $soyisim);
                        $initial = mb_substr($isim, 0, 1, 'UTF-8');
                        $pozisyon = htmlspecialchars($u['Pozisyon'] ?? $u['pozisyon'] ?? $u['position'] ?? '');
                        $bolum = htmlspecialchars($u['Bölüm'] ?? $u['department'] ?? $u['bolum'] ?? '-');
                        $yil = htmlspecialchars($u['MezuniyetYılı'] ?? $u['graduationYear'] ?? $u['mezuniyet_yili'] ?? '-');
                        $sehir = htmlspecialchars($u['Şehir'] ?? $u['city'] ?? $u['sehir'] ?? '-');
                    ?>
                    <tr>
                        <td>
                            <span style="font-weight: 700; color: var(--text-muted); font-size: 0.85rem;">#<?= $id ?></span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; flex-shrink: 0;">
                                    <?= $initial ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-main); font-size: 0.95rem;"><?= $tamAd ?></div>
                                    <?php if (!empty($pozisyon)): ?>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?= $pozisyon ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.82rem; font-weight: 500; color: #334155;">
                                <?= $bolum ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= $yil ?></strong>
                        </td>
                        <td style="color: var(--text-muted);">
                            📍 <?= $sehir ?>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 0.35rem;">
                                <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>" class="btn" style="padding: 0.35rem 0.75rem; font-size: 0.82rem; background: #0284c7;">Görüntüle</a>
                                <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>/edit" class="btn" style="padding: 0.35rem 0.75rem; font-size: 0.82rem; background: #eab308;">Düzenle</a>
                                <form action="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">
                                    <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.82rem;">Sil</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
