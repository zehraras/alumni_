<div class="card">
    <a href="<?= htmlspecialchars($prefix) ?>/applications" style="color:var(--text-muted); text-decoration:none; font-size:0.9rem;">&larr; Tüm Başvurulara Dön</a>

    <div style="margin-top:1rem; border-bottom:1px solid var(--border); padding-bottom:1rem;">
        <span class="badge" style="background:#fef3c7; color:#92400e;">Durum: <?= htmlspecialchars($app['status']) ?></span>
        <h1 style="margin:0.25rem 0;">Aday: <?= htmlspecialchars($app['applicant_name']) ?></h1>
        <p style="color:var(--text-muted);">📧 <?= htmlspecialchars($app['applicant_email']) ?> &bull; 📅 <?= htmlspecialchars($app['createdAt'] ?? '-') ?></p>
    </div>

    <div style="margin: 1.5rem 0;">
        <h3>Başvurulan Pozisyon</h3>
        <p style="margin-top:0.25rem; font-size:1.1rem; font-weight:600; color:var(--primary);">
            <?= htmlspecialchars($app['job_title'] ?? ('İlan #' . $app['job_posting_id'])) ?>
            <span style="color:var(--text-muted); font-size:0.95rem; font-weight:normal;">(<?= htmlspecialchars($app['job_company'] ?? '') ?>)</span>
        </p>

        <?php if (!empty($app['resume_url'])): ?>
            <h3 style="margin-top:1.5rem;">Özgeçmiş / Profil Bağlantısı</h3>
            <p style="margin-top:0.25rem;">
                <a href="<?= htmlspecialchars($app['resume_url']) ?>" target="_blank" style="color:var(--primary); font-weight:600;">
                    🔗 <?= htmlspecialchars($app['resume_url']) ?>
                </a>
            </p>
        <?php endif; ?>

        <?php if (!empty($app['cover_letter'])): ?>
            <h3 style="margin-top:1.5rem;">Ön Yazı / Aday Notu</h3>
            <div style="background:#f8fafc; border:1px solid var(--border); padding:1rem; border-radius:6px; margin-top:0.5rem; white-space:pre-wrap;">
                <?= htmlspecialchars($app['cover_letter']) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
