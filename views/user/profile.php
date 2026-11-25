<?php
// User Specific Profile View
$updateActionUrl = $prefix . '/profile/update';
$role = $currentUser['role'] ?? 'alumni';
?>

<div style="max-width: 960px; margin: 0 auto;">
    <?php if (!empty($flashSuccess)): ?>
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 0.85rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>✅</span>
            <span><?= htmlspecialchars($flashSuccess) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($flashError)): ?>
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.85rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>⚠️</span>
            <span><?= htmlspecialchars($flashError) ?></span>
        </div>
    <?php endif; ?>

    <!-- User Header Banner Card -->
    <div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 800; border: 2px solid #bfdbfe;">
                    <?= strtoupper(mb_substr($currentUser['isim'] ?? 'U', 0, 1, 'UTF-8')) ?>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); margin: 0;">
                            <?= htmlspecialchars(($currentUser['İsim'] ?? $currentUser['isim'] ?? '') . ' ' . ($currentUser['Soyisim'] ?? $currentUser['soyisim'] ?? '')) ?>
                        </h1>
                        <?php if ($role === 'admin'): ?>
                            <span class="badge" style="background: #fee2e2; color: #991b1b;">🛡️ Sistem Yöneticisi</span>
                        <?php elseif ($role === 'student'): ?>
                            <span class="badge" style="background: #fef3c7; color: #92400e;">🎒 Öğrenci</span>
                        <?php else: ?>
                            <span class="badge" style="background: #e0f2fe; color: #0369a1;">🎓 Mezun Üye</span>
                        <?php endif; ?>
                    </div>
                    <div style="color: var(--text-muted); font-size: 0.92rem; margin-top: 0.35rem;">
                        ✉️ <?= htmlspecialchars($currentUser['email'] ?? 'E-posta belirtilmedi') ?> &bull; 
                        💼 <?= htmlspecialchars($currentUser['Pozisyon'] ?? $currentUser['pozisyon'] ?? 'Pozisyon belirtilmedi') ?>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 0.6rem;">
                <?php if ($role === 'admin'): ?>
                    <a href="<?= htmlspecialchars($prefix) ?>/admin" class="btn" style="background: #991b1b;">
                        🛡️ Yönetim Paneline Git
                    </a>
                <?php endif; ?>
                <a href="<?= htmlspecialchars($prefix) ?>/logout" class="btn btn-secondary">
                    🚪 Çıkış Yap
                </a>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Left Column: User Specific Applications -->
        <div>
            <div class="card" style="height: 100%;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main);">
                        📬 Başvurularım (My Applications)
                    </h2>
                    <span class="badge"><?= count($applications) ?> Başvuru</span>
                </div>

                <?php if (empty($applications)): ?>
                    <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted);">
                        <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">📄</div>
                        <p style="font-size: 0.95rem;">Henüz bir iş ilanına başvurmadınız.</p>
                        <a href="<?= htmlspecialchars($prefix) ?>/job-postings" class="btn btn-secondary" style="margin-top: 1rem; font-size: 0.85rem;">
                            İş İlanlarını Keşfet &rarr;
                        </a>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                        <?php foreach ($applications as $app): ?>
                            <?php 
                                $status = $app['status'] ?? 'Pending';
                                $badgeBg = '#f1f5f9';
                                $badgeColor = '#475569';
                                if ($status === 'Accepted') { $badgeBg = '#dcfce7'; $badgeColor = '#15803d'; }
                                elseif ($status === 'Reviewed') { $badgeBg = '#e0f2fe'; $badgeColor = '#0369a1'; }
                                elseif ($status === 'Rejected') { $badgeBg = '#fee2e2'; $badgeColor = '#b91c1c'; }
                            ?>
                            <div style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1rem; background: #fafafa;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 0.98rem;">
                                        <?= htmlspecialchars($app['job_title'] ?? 'İş İlanı #' . ($app['job_posting_id'] ?? '')) ?>
                                    </div>
                                    <span class="badge" style="background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; font-size: 0.75rem;">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                                    🏢 <?= htmlspecialchars($app['job_company'] ?? 'Şirket') ?> &bull; 
                                    📅 <?= !empty($app['created_at']) ? date('d.m.Y H:i', strtotime($app['created_at'])) : '—' ?>
                                </div>
                                <div style="font-size: 0.82rem;">
                                    <a href="<?= htmlspecialchars($prefix) ?>/applications/<?= $app['id'] ?>" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                        Başvuru Detayını Gör &rarr;
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Edit Profile Form -->
        <div>
            <div class="card" style="height: 100%;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 1.25rem;">
                    ⚙️ Profil Bilgilerini Güncelle
                </h2>

                <form action="<?= htmlspecialchars($updateActionUrl) ?>" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="isim">Adınız <span style="color: var(--danger);">*</span></label>
                            <input type="text" id="isim" name="isim" value="<?= htmlspecialchars($currentUser['İsim'] ?? $currentUser['isim'] ?? '') ?>" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="soyisim">Soyadınız</label>
                            <input type="text" id="soyisim" name="soyisim" value="<?= htmlspecialchars($currentUser['Soyisim'] ?? $currentUser['soyisim'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label>E-posta (Değiştirilemez)</label>
                        <input type="text" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" disabled style="background: #f1f5f9; cursor: not-allowed;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="bolum">Bölüm</label>
                            <input type="text" id="bolum" name="bolum" value="<?= htmlspecialchars($currentUser['Bölüm'] ?? $currentUser['bolum'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="mezuniyet_yili">Mezuniyet Yılı</label>
                            <input type="number" id="mezuniyet_yili" name="mezuniyet_yili" value="<?= htmlspecialchars($currentUser['MezuniyetYılı'] ?? $currentUser['mezuniyet_yili'] ?? '') ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="sehir">Şehir</label>
                            <input type="text" id="sehir" name="sehir" value="<?= htmlspecialchars($currentUser['Şehir'] ?? $currentUser['sehir'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="pozisyon">Pozisyon / Ünvan</label>
                            <input type="text" id="pozisyon" name="pozisyon" value="<?= htmlspecialchars($currentUser['Pozisyon'] ?? $currentUser['pozisyon'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="new_password">Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
                        <input type="password" id="new_password" name="new_password" placeholder="En az 6 karakter">
                    </div>

                    <button type="submit" class="btn" style="width: 100%; justify-content: center;">
                        💾 Değişiklikleri Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
