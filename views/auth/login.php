<?php
// Login View
$actionUrl = $prefix . '/login';
$registerUrl = $prefix . '/register';
?>

<div style="max-width: 480px; margin: 1.5rem auto;">
    <div class="card" style="padding: 2.5rem 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔐</div>
            <h1 style="font-size: 1.85rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px;">
                Giriş Yap
            </h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.35rem;">
                Alumni Yönetim Sistemine hoş geldiniz.
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.85rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>⚠️</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 0.85rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>✅</span>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= htmlspecialchars($actionUrl) ?>" method="POST">
            <?php if (!empty($redirect)): ?>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="email">E-posta Adresi</label>
                <input type="email" id="email" name="email" placeholder="ornek@alumni.edu" required autofocus>
            </div>

            <div class="form-group" style="margin-bottom: 1.75rem;">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn" style="width: 100%; padding: 0.85rem; font-size: 1rem; justify-content: center;">
                Giriş Yap &rarr;
            </button>
        </form>

        <!-- Quick Demo Credentials Box -->
        <div style="margin-top: 2rem; background: #f8fafc; border: 1px dashed var(--border); border-radius: var(--radius-md); padding: 1rem; font-size: 0.85rem;">
            <div style="font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.35rem;">
                <span>💡</span> Hızlı Test Hesapları (Tıklayın)
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                <button type="button" onclick="fillCreds('zehra@alumni.edu', 'zehra123')" style="background: white; border: 1px solid var(--border); border-radius: 4px; padding: 0.35rem 0.6rem; text-align: left; cursor: pointer; font-size: 0.82rem;">
                    <strong>🛡️ Yönetici (Admin):</strong> zehra@alumni.edu / zehra123
                </button>
                <button type="button" onclick="fillCreds('ahmet.2@alumni.edu', 'password123')" style="background: white; border: 1px solid var(--border); border-radius: 4px; padding: 0.35rem 0.6rem; text-align: left; cursor: pointer; font-size: 0.82rem;">
                    <strong>🎓 Mezun (Alumni):</strong> ahmet.2@alumni.edu / password123
                </button>
            </div>
        </div>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border); color: var(--text-muted); font-size: 0.92rem;">
            Henüz bir hesabınız yok mu? 
            <a href="<?= htmlspecialchars($registerUrl) ?>" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                Kayıt Olun &rarr;
            </a>
        </div>
    </div>
</div>

<script>
function fillCreds(email, pwd) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pwd;
}
</script>
