<div class="card" style="max-width: 680px; margin: 0 auto;">
    <a href="<?= htmlspecialchars($prefix) ?>/users" style="color: var(--text-muted); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
        &larr; Mezunlar Listesine Dön
    </a>

    <div style="margin: 1rem 0 1.5rem;">
        <span class="badge" style="background: #dcfce7; color: #15803d;">➕ Yeni Kayıt</span>
        <h1 style="font-size: 1.75rem; font-weight: 800; margin-top: 0.35rem;">Yeni Mezun Kaydı Ekle</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Mezuniyet, bölüm ve kariyer bilgilerini girerek mezun rehberine kaydedin.</p>
    </div>
    
    <form action="<?= htmlspecialchars($prefix) ?>/users" method="POST">
        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="isimInput">İsim *</label>
                <input type="text" id="isimInput" name="İsim" placeholder="Örn: Zehra" required>
            </div>
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="soyisimInput">Soyisim</label>
                <input type="text" id="soyisimInput" name="Soyisim" placeholder="Örn: Aras">
            </div>
        </div>

        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="bolumInput">Bölüm</label>
                <input type="text" id="bolumInput" name="Bölüm" placeholder="Örn: Bilgisayar Mühendisliği">
            </div>
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="yilInput">Mezuniyet Yılı</label>
                <input type="number" id="yilInput" name="MezuniyetYılı" placeholder="Örn: 2026">
            </div>
        </div>

        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="pozisyonInput">Mevcut Pozisyon / Görev</label>
                <input type="text" id="pozisyonInput" name="Pozisyon" placeholder="Örn: Yazılım Mühendisi">
            </div>
            <div class="form-group" style="flex: 1; min-width: 220px;">
                <label for="sehirInput">Yaşadığı Şehir</label>
                <input type="text" id="sehirInput" name="Şehir" placeholder="Örn: İstanbul">
            </div>
        </div>
        
        <div style="margin-top: 2rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
            <a href="<?= htmlspecialchars($prefix) ?>/users" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn" style="background: #16a34a; padding: 0.65rem 1.75rem;">
                ✓ Mezun Kaydet
            </button>
        </div>
    </form>
</div>
