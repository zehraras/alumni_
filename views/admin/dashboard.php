<?php
// Admin Dashboard View
$roleActionUrl = $prefix . '/admin/users/role';
$deleteActionUrl = $prefix . '/admin/users/delete';
?>

<div style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge" style="background: #fee2e2; color: #b91c1c;">🛡️ Yönetici Yetkisi</span>
                <span style="font-size: 0.85rem; color: var(--text-muted);">Aktif Kullanıcı: <strong><?= htmlspecialchars($currentUser['isim'] . ' ' . ($currentUser['soyisim'] ?? '')) ?></strong></span>
            </div>
            <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px;">
                Sistem Yönetim Paneli
            </h1>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="<?= htmlspecialchars($prefix) ?>/users/create" class="btn">+ Yeni Mezun Ekle</a>
            <a href="<?= htmlspecialchars($prefix) ?>/job-postings/create" class="btn btn-secondary">+ Yeni İlan Ekle</a>
            <a href="<?= htmlspecialchars($prefix) ?>/announcements/create" class="btn btn-secondary">+ Yeni Duyuru Ekle</a>
        </div>
    </div>

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

    <!-- Overview Statistics Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="card" style="margin-bottom: 0; padding: 1.5rem;">
            <div style="font-size: 0.82rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 0.35rem;">
                👥 Toplam Kullanıcı
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary);">
                <?= $totalUsers ?>
            </div>
            <div style="display: flex; gap: 0.4rem; margin-top: 0.5rem; flex-wrap: wrap; font-size: 0.78rem;">
                <span class="badge" style="background: #fee2e2; color: #991b1b;"><?= $adminCount ?> Admin</span>
                <span class="badge" style="background: #e0f2fe; color: #0369a1;"><?= $alumniCount ?> Mezun</span>
                <span class="badge" style="background: #fef3c7; color: #92400e;"><?= $studentCount ?> Öğrenci</span>
            </div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 1.5rem;">
            <div style="font-size: 0.82rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 0.35rem;">
                💼 Aktif İş İlanları
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #0284c7;">
                <?= $totalJobs ?>
            </div>
            <div style="margin-top: 0.5rem; font-size: 0.85rem;">
                <a href="<?= htmlspecialchars($prefix) ?>/job-postings" style="color: var(--primary); font-weight: 600; text-decoration: none;">İlanları İncele &rarr;</a>
            </div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 1.5rem;">
            <div style="font-size: 0.82rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 0.35rem;">
                📢 Duyurular
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #059669;">
                <?= $totalAnnouncements ?>
            </div>
            <div style="margin-top: 0.5rem; font-size: 0.85rem;">
                <a href="<?= htmlspecialchars($prefix) ?>/announcements" style="color: #059669; font-weight: 600; text-decoration: none;">Duyuruları İncele &rarr;</a>
            </div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 1.5rem;">
            <div style="font-size: 0.82rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 0.35rem;">
                📬 Yapılan Başvurular
            </div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #7c3aed;">
                <?= $totalApplications ?>
            </div>
            <div style="margin-top: 0.5rem; font-size: 0.85rem;">
                <a href="<?= htmlspecialchars($prefix) ?>/applications" style="color: #7c3aed; font-weight: 600; text-decoration: none;">Başvuruları Yönet &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Users & Roles Manipulation Table -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-main);">
                    Kullanıcı ve Rol Yönetimi
                </h2>
                <p style="color: var(--text-muted); font-size: 0.88rem;">
                    Sistemdeki kullanıcıların rollerini değiştirebilir (Admin, Mezun, Öğrenci), profillerini düzenleyebilir veya silebilirsiniz.
                </p>
            </div>
            <span class="badge"><?= count($users) ?> Kayıtlı Kullanıcı</span>
        </div>

        <div class="table-responsive">
            <table class="user-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Kullanıcı Adı Soyadı</th>
                        <th>E-posta</th>
                        <th>Bölüm & Şehir</th>
                        <th>Mevcut Rol</th>
                        <th>Rol Değiştir (Manipulate Role)</th>
                        <th style="text-align: right;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <?php 
                            $uId = (int)$u['id'];
                            $uRole = $u['role'] ?? 'alumni';
                            $isSelf = ($uId === (int)$currentUser['id']);
                        ?>
                        <tr>
                            <td><strong>#<?= $uId ?></strong></td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">
                                    <?= htmlspecialchars($u['İsim'] ?? $u['isim'] ?? '') ?> <?= htmlspecialchars($u['Soyisim'] ?? $u['soyisim'] ?? '') ?>
                                    <?php if ($isSelf): ?>
                                        <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-size: 0.72rem; margin-left: 0.3rem;">Siz (You)</span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($u['Pozisyon'] ?? $u['pozisyon'] ?? 'Pozisyon belirtilmedi') ?>
                                </div>
                            </td>
                            <td>
                                <span style="font-family: monospace; font-size: 0.88rem; color: #475569;">
                                    <?= htmlspecialchars($u['email'] ?? '—') ?>
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 0.88rem;"><?= htmlspecialchars($u['Bölüm'] ?? $u['bolum'] ?? '—') ?></div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($u['Şehir'] ?? $u['sehir'] ?? '—') ?></div>
                            </td>
                            <td>
                                <?php if ($uRole === 'admin'): ?>
                                    <span class="badge" style="background: #fee2e2; color: #991b1b;">🛡️ Admin</span>
                                <?php elseif ($uRole === 'student'): ?>
                                    <span class="badge" style="background: #fef3c7; color: #92400e;">🎒 Öğrenci</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #e0f2fe; color: #0369a1;">🎓 Mezun</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="<?= htmlspecialchars($roleActionUrl) ?>" method="POST" style="display: flex; gap: 0.4rem; align-items: center;">
                                    <input type="hidden" name="user_id" value="<?= $uId ?>">
                                    <select name="role" style="padding: 0.35rem 0.5rem; border: 1px solid var(--border); border-radius: 4px; font-size: 0.82rem; background: white;">
                                        <option value="admin" <?= ($uRole === 'admin') ? 'selected' : '' ?>>Admin</option>
                                        <option value="alumni" <?= ($uRole === 'alumni') ? 'selected' : '' ?>>Mezun</option>
                                        <option value="student" <?= ($uRole === 'student') ? 'selected' : '' ?>>Öğrenci</option>
                                    </select>
                                    <button type="submit" class="btn" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;">
                                        Kaydet
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.4rem;">
                                    <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $uId ?>" class="btn btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;" title="Görüntüle">
                                        👁️
                                    </a>
                                    <a href="<?= htmlspecialchars($prefix) ?>/users/<?= $uId ?>/edit" class="btn btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;" title="Düzenle">
                                        ✏️
                                    </a>
                                    <?php if (!$isSelf): ?>
                                        <form action="<?= htmlspecialchars($deleteActionUrl) ?>" method="POST" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?= $uId ?>">
                                            <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;" title="Sil">
                                                🗑️
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
