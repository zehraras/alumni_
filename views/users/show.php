<?php
$id = htmlspecialchars($user['id'] ?? '-');
$isim = htmlspecialchars($user['İsim'] ?? $user['name'] ?? $user['isim'] ?? 'İsimsiz');
$soyisim = htmlspecialchars($user['Soyisim'] ?? $user['surname'] ?? '');
$tamAd = trim($isim . ' ' . $soyisim);
$bolum = htmlspecialchars($user['Bölüm'] ?? $user['department'] ?? $user['bolum'] ?? '-');
$yil = htmlspecialchars($user['MezuniyetYılı'] ?? $user['graduationYear'] ?? $user['mezuniyet'] ?? '-');
$sehir = htmlspecialchars($user['Şehir'] ?? $user['city'] ?? $user['email'] ?? '-');
$olusturulma = htmlspecialchars($user['createdAt'] ?? '-');
$guncellenme = htmlspecialchars($user['updatedAt'] ?? '-');
?>
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <span class="badge">🎓 Tekil Mezun Profili (Görsel Arayüz)</span>
            <h1><?= $tamAd ?></h1>
            <p style="color: var(--text-muted); margin: 0;">Sistem Kayıt ID: <strong>#<?= $id ?></strong></p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>/edit" class="btn" style="background: #eab308;">Düzenle</a>
            <a href="<?= htmlspecialchars($prefix) ?>/api/users/<?= $id ?>" target="_blank" class="btn" style="background: #10b981;">JSON Gör</a>
            <a href="<?= htmlspecialchars($prefix) ?>/users" class="btn" style="background: var(--text-muted);">Listeye Dön</a>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; background: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border);">
        <div>
            <div style="font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Bölüm</div>
            <div style="font-weight: 600; font-size: 1.1rem;"><?= $bolum ?></div>
        </div>
        <div>
            <div style="font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Mezuniyet Yılı</div>
            <div style="font-weight: 600; font-size: 1.1rem;"><?= $yil ?></div>
        </div>
        <div>
            <div style="font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Şehir / Konum</div>
            <div style="font-weight: 600; font-size: 1.1rem;"><?= $sehir ?></div>
        </div>
        <div>
            <div style="font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Kayıt Tarihi</div>
            <div style="font-weight: 600; font-size: 1rem;"><?= $olusturulma ?></div>
        </div>
    </div>
</div>
