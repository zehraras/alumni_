<div class="card">
    <div style="margin-bottom: 2rem;">
        <span class="badge" style="background:#fef3c7; color:#92400e;">📥 Başvuru Takip Paneli</span>
        <h1 style="font-size: 1.75rem; font-weight: 800; margin-top: 0.25rem;">Gelen İş & Staj Başvuruları</h1>
        <p style="color:var(--text-muted); font-size: 0.9rem;">Mezunların veya adayların sistemdeki açık ilanlara yaptığı başvurular ve özgeçmiş kayıtları.</p>
    </div>

    <?php if (empty($applications)): ?>
        <div style="text-align:center; padding: 3rem; color:var(--text-muted);">
            Henüz sisteme iletilmiş bir iş başvurusu bulunmamaktadır.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="user-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Aday & İletişim</th>
                        <th>Başvurulan İlan</th>
                        <th>Şirket</th>
                        <th>Durum</th>
                        <th>Başvuru Tarihi</th>
                        <th style="text-align: right;">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): 
                        $statusColor = match($app['status'] ?? '') {
                            'Accepted' => 'background:#dcfce7; color:#15803d;',
                            'Reviewed' => 'background:#e0e7ff; color:#3730a3;',
                            'Rejected' => 'background:#fee2e2; color:#b91c1c;',
                            default => 'background:#fef3c7; color:#92400e;'
                        };
                    ?>
                        <tr>
                            <td><span style="font-weight: 700; color: var(--text-muted); font-size: 0.85rem;">#<?= $app['id'] ?></span></td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main); font-size: 0.95rem;">
                                    <?= htmlspecialchars($app['applicant_name']) ?>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($app['applicant_email']) ?>
                                </div>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($app['job_title'] ?? ('İlan #' . $app['job_posting_id'])) ?></strong>
                            </td>
                            <td>
                                🏢 <?= htmlspecialchars($app['job_company'] ?? '-') ?>
                            </td>
                            <td>
                                <span style="<?= $statusColor ?> padding:0.25rem 0.6rem; border-radius:999px; font-size:0.75rem; font-weight:700;">
                                    <?= htmlspecialchars($app['status'] ?? 'Pending') ?>
                                </span>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-muted);">
                                📅 <?= htmlspecialchars($app['createdAt'] ?? '-') ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= htmlspecialchars($prefix) ?>/applications/<?= $app['id'] ?>" class="btn" style="padding: 0.35rem 0.75rem; font-size: 0.82rem; background: #0284c7;">
                                    Detay &rarr;
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
