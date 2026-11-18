<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <span class="badge" style="background:#e0e7ff; color:#3730a3;">💼 Kariyer & İstihdam</span>
            <h1 style="font-size: 1.75rem; font-weight: 800; margin-top: 0.25rem;">İş ve Staj İlanları</h1>
            <p style="color:var(--text-muted); font-size: 0.9rem;">Mezunlarımızın ve anlaşmalı kurumlarımızın paylaştığı aktif kariyer fırsatları.</p>
        </div>
        <a href="<?= htmlspecialchars($prefix) ?>/job-postings/create" class="btn" style="background:#16a34a;">
            + Yeni İlan Paylaş
        </a>
    </div>

    <?php if (empty($jobPostings)): ?>
        <div style="text-align:center; padding: 3rem; color:var(--text-muted);">
            Henüz yayınlanmış bir iş veya staj ilanı bulunmamaktadır.
        </div>
    <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:1.25rem;">
            <?php foreach ($jobPostings as $job): ?>
                <div style="border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; background: #fff; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1.25rem; transition: all 0.2s ease; box-shadow: var(--shadow-sm);">
                    <div style="flex: 1; min-width: 280px;">
                        <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:0.4rem; flex-wrap: wrap;">
                            <h3 style="margin:0; font-size:1.2rem; font-weight: 700; color:var(--text-main);">
                                <?= htmlspecialchars($job['title']) ?>
                            </h3>
                            <span style="background:#eff6ff; color:#1e40af; padding:0.2rem 0.6rem; border-radius:999px; font-size:0.75rem; font-weight:700;">
                                <?= htmlspecialchars($job['job_type']) ?>
                            </span>
                        </div>
                        <div style="margin:0.25rem 0 0.5rem; font-weight:600; color:#334155; font-size:0.95rem;">
                            🏢 <?= htmlspecialchars($job['company']) ?> 
                            <?php if (!empty($job['location'])): ?>
                                &bull; <span style="font-weight:normal; color:var(--text-muted);">📍 <?= htmlspecialchars($job['location']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($job['salary_range'])): ?>
                                &bull; <span style="font-weight:600; color:#15803d;">💰 <?= htmlspecialchars($job['salary_range']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($job['description'])): ?>
                            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0 0 0.5rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($job['description']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($job['author_name'])): ?>
                            <div style="font-size:0.82rem; color:var(--text-muted);">
                                👤 İlan Sahibi Mezun: <strong><?= htmlspecialchars($job['author_name']) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <a href="<?= htmlspecialchars($prefix) ?>/job-postings/<?= $job['id'] ?>" class="btn" style="padding:0.6rem 1.25rem; font-size:0.9rem; background: #0284c7;">
                            Detay & Başvur &rarr;
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
