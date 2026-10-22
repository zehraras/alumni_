<div class="card">
    <a href="<?= htmlspecialchars($prefix) ?>/job-postings" style="color:var(--text-muted); text-decoration:none; font-size:0.9rem;">&larr; Tüm İlanlara Dön</a>
    <h1 style="margin: 1rem 0;">Yeni İş İlanı Paylaş</h1>

    <form action="<?= htmlspecialchars($prefix) ?>/job-postings/create" method="POST">
        <div class="form-group">
            <label>İlanı Yayınlayan Mezun</label>
            <select name="user_id" style="width:100%; padding:0.75rem; border:1px solid var(--border); border-radius:6px;">
                <option value="">-- Kurumsal / Anonim --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>">
                        #<?= $u['id'] ?> - <?= htmlspecialchars($u['İsim'] ?? $u['isim']) ?> <?= htmlspecialchars($u['Soyisim'] ?? $u['soyisim'] ?? '') ?> (<?= htmlspecialchars($u['Bölüm'] ?? $u['bolum'] ?? '') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Pozisyon Başlığı *</label>
            <input type="text" name="title" required placeholder="Örn: Kıdemli PHP Geliştirici">
        </div>

        <div class="flex-gap">
            <div class="form-group" style="flex:1;">
                <label>Şirket / Kurum Adı *</label>
                <input type="text" name="company" required placeholder="Örn: Trendyol">
            </div>
            <div class="form-group" style="flex:1;">
                <label>Konum</label>
                <input type="text" name="location" placeholder="Örn: İstanbul (Hibrit)">
            </div>
        </div>

        <div class="flex-gap">
            <div class="form-group" style="flex:1;">
                <label>Çalışma Şekli</label>
                <select name="job_type" style="width:100%; padding:0.75rem; border:1px solid var(--border); border-radius:6px;">
                    <option value="Full-time">Tam Zamanlı (Full-time)</option>
                    <option value="Remote">Uzaktan (Remote)</option>
                    <option value="Hybrid">Hibrit (Hybrid)</option>
                    <option value="Part-time">Yarı Zamanlı (Part-time)</option>
                    <option value="Internship">Staj (Internship)</option>
                </select>
            </div>
            <div class="form-group" style="flex:1;">
                <label>Maaş Aralığı</label>
                <input type="text" name="salary_range" placeholder="Örn: 60.000 - 80.000 TL">
            </div>
        </div>

        <div class="form-group">
            <label>İş Açıklaması *</label>
            <textarea name="description" rows="4" required style="width:100%; padding:0.75rem; border:1px solid var(--border); border-radius:6px;" placeholder="Pozisyon görev ve sorumlulukları..."></textarea>
        </div>

        <div class="form-group">
            <label>Aranan Nitelikler</label>
            <textarea name="requirements" rows="3" style="width:100%; padding:0.75rem; border:1px solid var(--border); border-radius:6px;" placeholder="Beklenen teknolojiler ve tecrübeler..."></textarea>
        </div>

        <button type="submit" class="btn" style="background:#16a34a; font-size:1rem; padding:0.75rem 2rem;">İlanı Yayınla</button>
    </form>
</div>
