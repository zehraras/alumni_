<div class="card">
    <div style="margin-bottom: 1.5rem;">
        <span class="badge" style="background:#fef3c7; color:#92400e;">📥 Başvuru Yönetimi</span>
        <h1>Tüm İş Başvuruları</h1>
        <p style="color:var(--text-muted);">Sistemdeki ilanlara yapılan aday başvuruları.</p>
    </div>

    <?php if (empty($applications)): ?>
        <p style="text-align:center; padding: 2rem; color:var(--text-muted);">Henüz hiç başvuru yapılmamış.</p>
    <?php else: ?>
        <table class="user-table">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Aday</th>
                    <th>Başvurulan İlan</th>
                    <th>Şirket</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><strong>#<?= $app['id'] ?></strong></td>
                        <td>
                            <strong><?= htmlspecialchars($app['applicant_name']) ?></strong><br>
                            <span style="font-size:0.8rem; color:var(--text-muted);"><?= htmlspecialchars($app['applicant_email']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($app['job_title'] ?? ('İlan #' . $app['job_posting_id'])) ?></td>
                        <td><?= htmlspecialchars($app['job_company'] ?? '-') ?></td>
                        <td>
                            <span style="background:#f1f5f9; padding:0.2rem 0.5rem; border-radius:4px; font-size:0.8rem; font-weight:600;">
                                <?= htmlspecialchars($app['status']) ?>
                            </span>
                        </td>
                        <td style="font-size:0.85rem; color:var(--text-muted);"><?= htmlspecialchars($app['createdAt'] ?? '-') ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($prefix) ?>/applications/<?= $app['id'] ?>" class="btn" style="padding:0.3rem 0.6rem; font-size:0.85rem; background:#0284c7;">Detay</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
