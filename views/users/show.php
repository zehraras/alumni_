<?php
$id = htmlspecialchars($user['id'] ?? '-');
$isim = htmlspecialchars($user['İsim'] ?? $user['name'] ?? $user['isim'] ?? 'İsimsiz');
$soyisim = htmlspecialchars($user['Soyisim'] ?? $user['surname'] ?? '');
$tamAd = trim($isim . ' ' . $soyisim);
$initial = mb_substr($isim, 0, 1, 'UTF-8');
$bolum = htmlspecialchars($user['Bölüm'] ?? $user['department'] ?? $user['bolum'] ?? '-');
$yil = htmlspecialchars($user['MezuniyetYılı'] ?? $user['graduationYear'] ?? $user['mezuniyet_yili'] ?? '-');
$pozisyon = htmlspecialchars($user['Pozisyon'] ?? $user['pozisyon'] ?? $user['position'] ?? '-');
$sehir = htmlspecialchars($user['Şehir'] ?? $user['city'] ?? $user['sehir'] ?? '-');
$olusturulma = htmlspecialchars($user['createdAt'] ?? '-');
?>
<div class="card">
    <a href="<?= htmlspecialchars($prefix) ?>/users" style="color: var(--text-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        &larr; Tüm Mezunlara Dön
    </a>

    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem; margin: 1.5rem 0 2rem;">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #3b82f6); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 800; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);">
                <?= $initial ?>
            </div>
            <div>
                <span class="badge" style="background: #eff6ff; color: #1e40af;">🎓 Mezun Profili #<?= $id ?></span>
                <h1 style="font-size: 2rem; font-weight: 800; margin: 0.25rem 0;"><?= $tamAd ?></h1>
                <p style="color: var(--text-muted); font-size: 1rem; margin: 0;"><?= $pozisyon !== '-' ? $pozisyon : $bolum ?></p>
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>/edit" class="btn" style="background: #eab308;">
                ✏️ Düzenle
            </a>
            <a href="<?= htmlspecialchars($prefix) ?>/api/users/<?= $id ?>" target="_blank" class="btn btn-secondary">
                { } JSON Gör
            </a>
            <form action="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Bu profili silmek istediğinize emin misiniz?');">
                <button type="submit" class="btn btn-danger">🗑️ Sil</button>
            </form>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border);">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Mezun Olunan Bölüm</div>
            <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-top: 0.25rem;"><?= $bolum ?></div>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Mezuniyet Dönemi</div>
            <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-top: 0.25rem;">🎓 <?= $yil ?></div>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Mevcut Pozisyon</div>
            <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-top: 0.25rem;">💼 <?= $pozisyon ?></div>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Şehir / Lokasyon</div>
            <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-top: 0.25rem;">📍 <?= $sehir ?></div>
        </div>
        <div style="grid-column: 1 / -1; border-top: 1px solid var(--border); padding-top: 0.75rem; font-size: 0.85rem; color: var(--text-muted);">
            Kayıt Oluşturulma: <?= $olusturulma ?>
        </div>
    </div>
</div>
