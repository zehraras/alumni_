<?php
$id = htmlspecialchars($user['id'] ?? '');
$isim = htmlspecialchars($user['İsim'] ?? $user['name'] ?? $user['isim'] ?? '');
$soyisim = htmlspecialchars($user['Soyisim'] ?? $user['surname'] ?? '');
$bolum = htmlspecialchars($user['Bölüm'] ?? $user['department'] ?? $user['bolum'] ?? '');
$yil = htmlspecialchars($user['MezuniyetYılı'] ?? $user['graduationYear'] ?? $user['mezuniyet'] ?? '');
$sehir = htmlspecialchars($user['Şehir'] ?? $user['city'] ?? $user['email'] ?? '');
?>
<div class="card">
    <span class="badge">🌐 Web Arayüzü (URL/users/<?= $id ?>/edit)</span>
    <h1>✏️ Mezun Bilgilerini Düzenle</h1>
    
    <form action="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>/update" method="POST" style="margin-top: 2rem;">
        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="isimInput">İsim</label>
                <input type="text" id="isimInput" name="İsim" value="<?= $isim ?>" required>
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="soyisimInput">Soyisim</label>
                <input type="text" id="soyisimInput" name="Soyisim" value="<?= $soyisim ?>">
            </div>
        </div>
        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="bolumInput">Bölüm</label>
                <input type="text" id="bolumInput" name="Bölüm" value="<?= $bolum ?>">
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="yilInput">Mezuniyet Yılı</label>
                <input type="number" id="yilInput" name="MezuniyetYılı" value="<?= $yil ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="sehirInput">Şehir</label>
            <input type="text" id="sehirInput" name="Şehir" value="<?= $sehir ?>">
        </div>
        
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn">💾 Değişiklikleri Kaydet (POST /users/<?= $id ?>/update)</button>
            <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $id ?>" class="btn" style="background: var(--text-muted);">İptal</a>
        </div>
    </form>
</div>
