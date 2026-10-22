<div class="card">
    <a href="<?= htmlspecialchars($prefix) ?>/job-postings" style="color:var(--text-muted); text-decoration:none; font-size:0.9rem;">&larr; Tüm İlanlara Dön</a>
    
    <?php if (isset($_GET['applied'])): ?>
        <div style="background:#dcfce7; color:#15803d; padding:1rem; border-radius:6px; margin: 1rem 0; font-weight:600;">
            🎉 Başvurunuz başarıyla iletildi!
        </div>
    <?php endif; ?>

    <div style="margin-top:1rem; border-bottom:1px solid var(--border); padding-bottom:1rem;">
        <span class="badge" style="background:#e0e7ff; color:#3730a3;"><?= htmlspecialchars($job['job_type']) ?></span>
        <h1 style="margin:0.25rem 0;"><?= htmlspecialchars($job['title']) ?></h1>
        <h3 style="color:var(--text-muted); margin:0;">🏢 <?= htmlspecialchars($job['company']) ?> &bull; 📍 <?= htmlspecialchars($job['location']) ?></h3>
        <?php if (!empty($job['salary_range'])): ?>
            <p style="color:#15803d; font-weight:600; margin-top:0.5rem;">💰 Maaş: <?= htmlspecialchars($job['salary_range']) ?></p>
        <?php endif; ?>
    </div>

    <div style="margin: 1.5rem 0;">
        <h3>Pozisyon Açıklaması</h3>
        <p style="white-space: pre-wrap; margin-top:0.5rem;"><?= htmlspecialchars($job['description']) ?></p>

        <?php if (!empty($job['requirements'])): ?>
            <h3 style="margin-top:1.5rem;">Aranan Nitelikler</h3>
            <p style="white-space: pre-wrap; margin-top:0.5rem;"><?= htmlspecialchars($job['requirements']) ?></p>
        <?php endif; ?>
    </div>

    <!-- Application Form -->
    <div style="background:#f8fafc; border:1px solid var(--border); border-radius:8px; padding:1.5rem; margin-top:2rem;">
        <h3 style="margin-bottom:1rem;">🚀 Bu Pozisyona Başvur</h3>
        <form action="<?= htmlspecialchars($prefix) ?>/job-postings/<?= $job['id'] ?>/apply" method="POST">
            <div class="form-group">
                <label>Adınız ve Soyadınız *</label>
                <input type="text" name="applicant_name" required placeholder="Örn: Ayşe Yılmaz">
            </div>
            <div class="form-group">
                <label>E-posta Adresiniz *</label>
                <input type="email" name="applicant_email" required placeholder="Örn: ayse@example.com">
            </div>
            <div class="form-group">
                <label>Özgeçmiş / LinkedIn / GitHub Bağlantısı</label>
                <input type="url" name="resume_url" placeholder="https://linkedin.com/in/...">
            </div>
            <div class="form-group">
                <label>Ön Yazı / Notunuz</label>
                <textarea name="cover_letter" rows="3" style="width:100%; padding:0.75rem; border:1px solid var(--border); border-radius:6px;" placeholder="Kendinizden ve bu pozisyona uygunluğunuzdan bahsedin..."></textarea>
            </div>
            <button type="submit" class="btn" style="background:#16a34a; font-size:1rem; padding:0.6rem 1.5rem;">Başvuruyu Gönder</button>
        </form>
    </div>

    <!-- Existing Applications count for this job -->
    <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border);">
        <h4>Bu İlana Yapılan Başvurular (<?= count($applications) ?>)</h4>
        <?php if (!empty($applications)): ?>
            <ul style="margin-top:0.5rem; padding-left:1.25rem;">
                <?php foreach ($applications as $a): ?>
                    <li style="margin-bottom:0.25rem;">
                        <strong><?= htmlspecialchars($a['applicant_name']) ?></strong> (<?= htmlspecialchars($a['status']) ?>) - <?= htmlspecialchars($a['createdAt']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="color:var(--text-muted); font-size:0.9rem; margin-top:0.25rem;">Henüz başvuru yapılmadı.</p>
        <?php endif; ?>
    </div>
</div>
