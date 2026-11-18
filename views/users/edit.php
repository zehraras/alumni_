<?php
$id = htmlspecialchars($user['id'] ?? '');
$isim = htmlspecialchars($user['İsim'] ?? $user['name'] ?? $user['isim'] ?? '');
$soyisim = htmlspecialchars($user['Soyisim'] ?? $user['surname'] ?? '');
$bolum = htmlspecialchars($user['Bölüm'] ?? $user['department'] ?? $user['bolum'] ?? '');
$yil = htmlspecialchars($user['MezuniyetYılı'] ?? $user['graduationYear'] ?? $user['mezuniyet_yili'] ?? '');
$pozisyon = htmlspecialchars($user['Pozisyon'] ?? $user['pozisyon'] ?? $user['position'] ?? '');
$sehir = htmlspecialchars($user['Şehir'] ?? $user['city'] ?? $user['sehir'] ?? '');
?>
<div class="card" style="max-width: 680px; margin: 0 auto;">
    <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>" style="color: var(--text-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        &larr; Mezun Profiline Dön
    </a>

    <div style="margin: 1rem 0 1.5rem;">
        <span class="badge" style="background: #fef9c3; color: #854d0e;">✏️ Kayıt Düzenleme</span>
        <h1 style="font-size: 1.75rem; font-weight: 800; margin-top: 0.35rem;">Mezun Bilgilerini Düzenle</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">#<?= $id ?> numaralı mezun profilinin bilgilerini güncelleyin.</p>
    </div>
    
    <form action="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>/update" method="POST">
        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="isimInput">İsim *</label>
                <input type="text" id="isimInput" name="İsim" value="<?= $isim ?>" required>
            </div>
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="soyisimInput">Soyisim</label>
                <input type="text" id="soyisimInput" name="Soyisim" value="<?= $soyisim ?>">
            </div>
        </div>

        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="bolumInput">Bölüm</label>
                <input type="text" id="bolumInput" name="Bölüm" value="<?= $bolum ?>">
            </div>
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="yilInput">Mezuniyet Yılı</label>
                <input type="number" id="yilInput" name="MezuniyetYılı" value="<?= $yil ?>">
            </div>
        </div>

        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="pozisyonInput">Pozisyon / Unvan</label>
                <input type="text" id="pozisyonInput" name="Pozisyon" value="<?= $pozisyon ?>">
            </div>
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="sehirInput">Şehir</label>
                <input type="text" id="sehirInput" name="Şehir" value="<?= $sehir ?>">
            </div>
        </div>
        
        <div style="margin-top: 2rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
            <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn" style="background: #eab308; padding: 0.65rem 1.75rem;">
                💾 Güncellemeleri Kaydet
            </button>
        </div>
    </form>
</div>
