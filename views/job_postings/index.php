<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <span class="badge" style="background:#e0e7ff; color:#3730a3;">💼 Kariyer & İstihdam</span>
            <h1>İş ve Staj İlanları</h1>
            <p style="color:var(--text-muted);">Mezunlarımızın ve anlaşmalı kurumlarımızın paylaştığı aktif kariyer fırsatları.</p>
        </div>
        <a href="<?= htmlspecialchars($prefix) ?>/job-postings/create" class="btn" style="background:#16a34a;">+ Yeni İlan Paylaş</a>
    </div>

    <?php if (empty($jobPostings)): ?>
        <p style="text-align:center; padding: 2rem; color:var(--text-muted);">Henüz yayınlanmış bir iş ilanı bulunmamaktadır.</p>
    <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:1rem;">
            <?php foreach ($jobPostings as $job): ?>
                <div style="border: 1px solid var(--border); border-radius: 8px; padding: 1.25rem; background: #fff; transition: transform 0.1s; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
                    <div>
                        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.25rem;">
                            <h3 style="margin:0; font-size:1.15rem; color:var(--primary);"><?= htmlspecialchars($job['title']) ?></h3>
                            <span style="background:#f1f5f9; color:#475569; padding:0.15rem 0.5rem; border-radius:4px; font-size:0.75rem; font-weight:600;">
                                <?= htmlspecialchars($job['job_type']) ?>
                            </span>
                        </div>
                        <p style="margin:0; font-weight:600; color:var(--text-main); font-size:0.95rem;">
                            🏢 <?= htmlspecialchars($job['company']) ?> 
                            <?php if (!empty($job['location'])): ?>
                                &bull; <span style="font-weight:normal; color:var(--text-muted);">📍 <?= htmlspecialchars($job['location']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($job['salary_range'])): ?>
                                &bull; <span style="font-weight:normal; color:#15803d;">💰 <?= htmlspecialchars($job['salary_range']) ?></span>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($job['author_name'])): ?>
                            <p style="margin-top:0.35rem; font-size:0.85rem; color:var(--text-muted);">
                                👤 İlan Sahibi Mezun: <strong><?= htmlspecialchars($job['author_name']) ?></strong>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <a href="<?= htmlspecialchars($prefix) ?>/job-postings/<?= $job['id'] ?>" class="btn" style="padding:0.4rem 0.8rem; font-size:0.875rem;">Detay & Başvur</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
