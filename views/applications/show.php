<?php
$statusColor = match($app['status'] ?? '') {
    'Accepted' => 'background:#dcfce7; color:#15803d;',
    'Reviewed' => 'background:#e0e7ff; color:#3730a3;',
    'Rejected' => 'background:#fee2e2; color:#b91c1c;',
    default => 'background:#fef3c7; color:#92400e;'
};
?>
<div class="card">
    <a href="<?= htmlspecialchars($prefix) ?>/applications" style="color: var(--text-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        &larr; Tüm Başvurulara Dön
    </a>

    <div style="margin: 1.25rem 0 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
        <span class="badge" style="<?= $statusColor ?>">Başvuru Durumu: <?= htmlspecialchars($app['status'] ?? 'Pending') ?></span>
        <h1 style="font-size: 2rem; font-weight: 800; margin: 0.5rem 0 0.25rem;">
            <?= htmlspecialchars($app['applicant_name']) ?>
        </h1>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">
            📧 <?= htmlspecialchars($app['applicant_email']) ?> &bull; 📅 Başvuru: <?= htmlspecialchars($app['createdAt'] ?? '-') ?>
        </p>
    </div>

    <div style="margin: 1.5rem 0; display: flex; flex-direction: column; gap: 1.5rem;">
        <div style="padding: 1.25rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #1e40af; text-transform: uppercase;">Başvurulan Pozisyon</div>
            <div style="font-size: 1.25rem; font-weight: 800; color: #1e3a8a; margin-top: 0.25rem;">
                <?= htmlspecialchars($app['job_title'] ?? ('İlan #' . $app['job_posting_id'])) ?>
                <span style="font-size: 1rem; font-weight: normal; color: #3b82f6;">&bull; 🏢 <?= htmlspecialchars($app['job_company'] ?? '') ?></span>
            </div>
            <div style="margin-top: 0.75rem;">
                <a href="<?= htmlspecialchars($prefix) ?>/job-postings/<?= (int)$app['job_posting_id'] ?>" class="btn" style="padding: 0.35rem 0.85rem; font-size: 0.82rem; background: #2563eb;">
                    İlan Detayını Aç &rarr;
                </a>
            </div>
        </div>

        <?php if (!empty($app['resume_url'])): ?>
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">Özgeçmiş / Portföy Bağlantısı</h3>
                <div style="padding: 1rem 1.25rem; background: #f8fafc; border: 1px solid var(--border); border-radius: 10px;">
                    <a href="<?= htmlspecialchars($app['resume_url']) ?>" target="_blank" rel="noopener noreferrer" style="color: #2563eb; font-weight: 600; text-decoration: none; word-break: break-all;">
                        🔗 <?= htmlspecialchars($app['resume_url']) ?> &nearr;
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($app['cover_letter'])): ?>
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">Aday Ön Yazısı</h3>
                <div style="padding: 1.25rem; background: #f8fafc; border: 1px solid var(--border); border-radius: 10px; line-height: 1.7; white-space: pre-wrap; font-size: 0.95rem;">
<?= htmlspecialchars($app['cover_letter']) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
