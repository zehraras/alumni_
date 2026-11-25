<?php
// Registration View
$actionUrl = $prefix . '/register';
$loginUrl = $prefix . '/login';
?>

<div style="max-width: 680px; margin: 0 auto;">
    <div class="card" style="padding: 2.5rem 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <span class="badge" style="margin-bottom: 0.75rem;">🎓 Mezun & Öğrenci Kaydı</span>
            <h1 style="font-size: 1.85rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px; margin-top: 0.5rem;">
                Aramıza Katılın
            </h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.35rem;">
                Alumni platformuna kaydolarak mezun ağımıza katılın, iş ilanlarını keşfedin ve kariyer fırsatlarını yakalayın.
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.85rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>⚠️</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= htmlspecialchars($actionUrl) ?>" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="isim">Adınız <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="isim" name="isim" value="<?= htmlspecialchars($formData['isim'] ?? '') ?>" placeholder="Örn: Zehra" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="soyisim">Soyadınız</label>
                    <input type="text" id="soyisim" name="soyisim" value="<?= htmlspecialchars($formData['soyisim'] ?? '') ?>" placeholder="Örn: Aras">
                </div>
            </div>

            <div class="form-group">
                <label for="email">E-posta Adresi <span style="color: var(--danger);">*</span></label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($formData['email'] ?? '') ?>" placeholder="ornek@alumni.edu" required>
                <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 0.25rem;">Giriş yaparken bu e-posta adresi kullanılacaktır.</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="password">Şifre <span style="color: var(--danger);">*</span></label>
                    <input type="password" id="password" name="password" placeholder="En az 6 karakter" required minlength="6">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="password_confirm">Şifre Tekrarı <span style="color: var(--danger);">*</span></label>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Şifrenizi tekrar giriniz" required minlength="6">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="bolum">Bölüm</label>
                    <input type="text" id="bolum" name="bolum" value="<?= htmlspecialchars($formData['bolum'] ?? '') ?>" placeholder="Örn: Bilgisayar Mühendisliği">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="mezuniyet_yili">Mezuniyet Yılı</label>
                    <input type="number" id="mezuniyet_yili" name="mezuniyet_yili" value="<?= htmlspecialchars($formData['mezuniyet_yili'] ?? '') ?>" placeholder="Örn: 2024" min="1970" max="2035">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="sehir">Yaşadığınız Şehir</label>
                    <input type="text" id="sehir" name="sehir" value="<?= htmlspecialchars($formData['sehir'] ?? '') ?>" placeholder="Örn: İstanbul">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="pozisyon">Mevcut Ünvan / Pozisyon</label>
                    <input type="text" id="pozisyon" name="pozisyon" value="<?= htmlspecialchars($formData['pozisyon'] ?? '') ?>" placeholder="Örn: Yazılım Geliştirici">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.75rem;">
                <label for="role">Üyelik Türü</label>
                <select id="role" name="role" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.95rem;">
                    <option value="alumni" <?= (($formData['role'] ?? '') === 'alumni') ? 'selected' : '' ?>>Mezun (Alumni)</option>
                    <option value="student" <?= (($formData['role'] ?? '') === 'student') ? 'selected' : '' ?>>Öğrenci (Student)</option>
                </select>
            </div>

            <button type="submit" class="btn" style="width: 100%; padding: 0.85rem; font-size: 1rem; justify-content: center;">
                ✨ Hesabımı Oluştur
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.75rem; padding-top: 1.5rem; border-top: 1px solid var(--border); color: var(--text-muted); font-size: 0.92rem;">
            Zaten bir hesabınız var mı? 
            <a href="<?= htmlspecialchars($loginUrl) ?>" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                Giriş Yapın &rarr;
            </a>
        </div>
    </div>
</div>
