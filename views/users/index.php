<div class="card">
    <span class="badge">🌐 Web Arayüzü (URL/users)</span>
    <h1>🎓 Kayıtlı Mezunlar & Kullanıcılar</h1>
    
    <div style="margin-bottom: 1rem; text-align: right;">
        <a href="<?= htmlspecialchars($prefix) ?>/users/create" class="btn">Yeni Mezun Ekle (GET /users/create)</a>
    </div>

    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>İsim / Soyisim</th>
                <th>Bölüm</th>
                <th>Mezuniyet Yılı</th>
                <th>Şehir / İletişim</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="6" style="text-align:center; color: var(--text-muted); padding: 2rem;">Henüz kayıtlı kullanıcı bulunmamaktadır.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $u): 
                    $id = htmlspecialchars($u['id'] ?? '-');
                    $isim = htmlspecialchars($u['İsim'] ?? $u['name'] ?? $u['isim'] ?? 'İsimsiz');
                    $soyisim = htmlspecialchars($u['Soyisim'] ?? $u['surname'] ?? '');
                    $tamAd = trim($isim . ' ' . $soyisim);
                    $bolum = htmlspecialchars($u['Bölüm'] ?? $u['department'] ?? $u['bolum'] ?? '-');
                    $yil = htmlspecialchars($u['MezuniyetYılı'] ?? $u['graduationYear'] ?? $u['mezuniyet'] ?? '-');
                    $sehir = htmlspecialchars($u['Şehir'] ?? $u['city'] ?? $u['email'] ?? '-');
                ?>
                <tr>
                    <td><strong>#<?= $id ?></strong></td>
                    <td><?= $tamAd ?></td>
                    <td><?= $bolum ?></td>
                    <td><?= $yil ?></td>
                    <td><?= $sehir ?></td>
                    <td>
                        <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>" class="btn" style="padding: 0.3rem 0.6rem; font-size: 0.85rem; background: #0284c7;">Görüntüle</a>
                        <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>/edit" class="btn" style="padding: 0.3rem 0.6rem; font-size: 0.85rem; background: #eab308;">Düzenle</a>
                        <form action="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                            <button type="submit" class="btn btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.85rem;">Sil</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
