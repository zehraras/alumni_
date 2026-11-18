<div class="card" style="max-width: 720px; margin: 0 auto;">
    <a href="<?= htmlspecialchars($prefix) ?>/job-postings" style="color: var(--text-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        &larr; Tüm İlanlara Dön
    </a>

    <div style="margin: 1rem 0 1.5rem;">
        <span class="badge" style="background:#e0e7ff; color:#3730a3;">💼 Kariyer Fırsatı</span>
        <h1 style="font-size: 1.75rem; font-weight: 800; margin-top: 0.35rem;">Yeni İş / Staj İlanı Paylaş</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Mezun topluluğumuzla şirketinizdeki açık kariyer fırsatlarını paylaşın.</p>
    </div>

    <form action="<?= htmlspecialchars($prefix) ?>/job-postings/create" method="POST">
        <div class="form-group">
            <label for="userInput">İlanı Yayınlayan Mezun (Opsiyonel)</label>
            <select id="userInput" name="user_id">
                <option value="">-- Kurumsal / Anonim İlan --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>">
                        #<?= $u['id'] ?> - <?= htmlspecialchars($u['İsim'] ?? $u['isim']) ?> <?= htmlspecialchars($u['Soyisim'] ?? $u['soyisim'] ?? '') ?> (<?= htmlspecialchars($u['Bölüm'] ?? $u['bolum'] ?? '') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="titleInput">Pozisyon Başlığı * (Maks. 120 karakter)</label>
            <input type="text" id="titleInput" name="title" maxlength="120" required placeholder="Örn: Kıdemli Full Stack PHP Geliştirici">
        </div>

        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="companyInput">Şirket / Kurum Adı *</label>
                <input type="text" id="companyInput" name="company" required placeholder="Örn: Trendyol">
            </div>
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="locationInput">Çalışma Konumu / Şehir (Opsiyonel)</label>
                <input type="text" id="locationInput" name="location" placeholder="Örn: İstanbul (Hibrit)">
            </div>
        </div>

        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="jobTypeInput">Çalışma Biçimi</label>
                <select id="jobTypeInput" name="job_type">
                    <option value="Full-time">Tam Zamanlı (Full-time)</option>
                    <option value="Remote">Uzaktan (Remote)</option>
                    <option value="Hybrid">Hibrit (Hybrid)</option>
                    <option value="Part-time">Yarı Zamanlı (Part-time)</option>
                    <option value="Internship">Staj (Internship)</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="salaryInput">Maaş Aralığı (Opsiyonel)</label>
                <input type="text" id="salaryInput" name="salary_range" placeholder="Örn: 70.000 - 90.000 TL">
            </div>
        </div>

        <div class="form-group">
            <label for="descInput">İş Açıklaması (Opsiyonel)</label>
            <textarea id="descInput" name="description" rows="4" placeholder="Pozisyon görevleri, projeler ve şirket hakkında detaylar..."></textarea>
        </div>

        <div class="form-group">
            <label for="reqInput">Aranan Nitelikler (Opsiyonel)</label>
            <textarea id="reqInput" name="requirements" rows="3" placeholder="Aranan teknolojiler, deneyim yılı ve yetkinlikler..."></textarea>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
            <a href="<?= htmlspecialchars($prefix) ?>/job-postings" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn" style="background: #16a34a; padding: 0.65rem 1.75rem;">
                🚀 İlanı Yayınla
            </button>
        </div>
    </form>
</div>
