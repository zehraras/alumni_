<div class="card">
    <a href="<?= htmlspecialchars($prefix) ?>/job-postings" style="color: var(--text-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        &larr; Tüm İlanlara Dön
    </a>
    
    <?php if (isset($_GET['applied'])): ?>
        <div style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 1rem 1.25rem; border-radius: 10px; margin: 1.25rem 0; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            🎉 Tebrikler! İş başvurunuz başarıyla iletildi.
        </div>
    <?php endif; ?>

    <div style="margin: 1.25rem 0 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
        <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
            <span class="badge" style="background:#eff6ff; color:#1e40af;">💼 <?= htmlspecialchars($job['job_type']) ?></span>
            <?php if (!empty($job['author_name'])): ?>
                <span class="badge" style="background:#f1f5f9; color:#475569;">👤 İlan Sahibi: <?= htmlspecialchars($job['author_name']) ?></span>
            <?php endif; ?>
        </div>
        <h1 style="font-size: 2.1rem; font-weight: 800; margin: 0.25rem 0; color: var(--text-main);"><?= htmlspecialchars($job['title']) ?></h1>
        <div style="font-size: 1.1rem; color: #334155; margin-top: 0.35rem; font-weight: 600;">
            🏢 <?= htmlspecialchars($job['company']) ?> 
            <?php if (!empty($job['location'])): ?>
                &bull; <span style="font-weight: normal; color: var(--text-muted);">📍 <?= htmlspecialchars($job['location']) ?></span>
            <?php endif; ?>
            <?php if (!empty($job['salary_range'])): ?>
                &bull; <span style="font-weight: 700; color: #15803d;">💰 <?= htmlspecialchars($job['salary_range']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin: 1.5rem 0; display: flex; flex-direction: column; gap: 1.5rem;">
        <?php if (!empty($job['description'])): ?>
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-main);">Pozisyon Açıklaması</h3>
                <div style="padding: 1.25rem; background: #f8fafc; border: 1px solid var(--border); border-radius: 10px; line-height: 1.7; white-space: pre-wrap; font-size: 0.95rem;">
<?= htmlspecialchars($job['description']) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($job['requirements'])): ?>
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-main);">Aranan Nitelikler</h3>
                <div style="padding: 1.25rem; background: #f8fafc; border: 1px solid var(--border); border-radius: 10px; line-height: 1.7; white-space: pre-wrap; font-size: 0.95rem;">
<?= htmlspecialchars($job['requirements']) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Application Form -->
    <div style="background: #ffffff; border: 2px solid #bfdbfe; border-radius: 14px; padding: 2rem; margin-top: 2rem; box-shadow: var(--shadow-sm);">
        <div style="margin-bottom: 1.25rem;">
            <span class="badge" style="background:#dbeafe; color:#1e40af;">Hemen Başvur</span>
            <h3 style="font-size: 1.35rem; font-weight: 800; margin-top: 0.25rem;">🚀 Bu Pozisyona Başvuru Yap</h3>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Profiliniz ve iletişim bilgileriniz doğrudan işverene ulaştırılacaktır.</p>
        </div>
        <form action="<?= htmlspecialchars($prefix) ?>/job-postings/<?= $job['id'] ?>/apply" method="POST">
            <div class="flex-gap">
                <div class="form-group" style="flex: 1; min-width: 220px;">
                    <label for="appNameInput">Adınız ve Soyadınız *</label>
                    <input type="text" id="appNameInput" name="applicant_name" required placeholder="Örn: Ayşe Yılmaz">
                </div>
                <div class="form-group" style="flex: 1; min-width: 220px;">
                    <label for="appEmailInput">E-posta Adresiniz *</label>
                    <input type="email" id="appEmailInput" name="applicant_email" required placeholder="Örn: ayse@example.com">
                </div>
            </div>
            <div class="form-group">
                <label for="resumeInput">Özgeçmiş / LinkedIn / GitHub Bağlantısı</label>
                <input type="url" id="resumeInput" name="resume_url" placeholder="https://linkedin.com/in/... veya https://github.com/...">
            </div>
            <div class="form-group">
                <label for="coverInput">Ön Yazı / Başvuru Notunuz</label>
                <textarea id="coverInput" name="cover_letter" rows="3" placeholder="Kendinizden, deneyimlerinizden ve bu pozisyona uygunluğunuzdan bahsedin..."></textarea>
            </div>
            <button type="submit" class="btn" style="background: #16a34a; font-size: 1rem; padding: 0.75rem 2rem;">
                ✓ Başvuruyu Gönder
            </button>
        </form>
    </div>

    <!-- Applications History -->
    <div style="margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
        <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.75rem;">
            Bu İlana Yapılan Başvurular (<?= count($applications) ?>)
        </h4>
        <?php if (!empty($applications)): ?>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <?php foreach ($applications as $a): ?>
                    <div style="padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <strong><?= htmlspecialchars($a['applicant_name']) ?></strong> 
                            <span style="font-size: 0.85rem; color: var(--text-muted);">&bull; <?= htmlspecialchars($a['applicant_email']) ?></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="background: #e2e8f0; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                <?= htmlspecialchars($a['status']) ?>
                            </span>
                            <small style="color: var(--text-muted);"><?= htmlspecialchars($a['createdAt']) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Henüz bir başvuru kaydedilmedi.</p>
        <?php endif; ?>
    </div>
</div>
