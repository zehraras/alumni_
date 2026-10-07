<div class="card">
    <span class="badge">🌐 Web Arayüzü (URL/users/create)</span>
    <h1>➕ Yeni Mezun Ekle</h1>
    
    <form action="<?= htmlspecialchars($prefix) ?>/users" method="POST" style="margin-top: 2rem;">
        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="isimInput">İsim</label>
                <input type="text" id="isimInput" name="İsim" placeholder="Örn: Zehra" required>
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="soyisimInput">Soyisim</label>
                <input type="text" id="soyisimInput" name="Soyisim" placeholder="Örn: Aras">
            </div>
        </div>
        <div class="flex-gap">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="bolumInput">Bölüm</label>
                <input type="text" id="bolumInput" name="Bölüm" placeholder="Örn: Bilgisayar Mühendisliği">
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="yilInput">Mezuniyet Yılı</label>
                <input type="number" id="yilInput" name="MezuniyetYılı" placeholder="Örn: 2026">
            </div>
        </div>
        <div class="form-group">
            <label for="sehirInput">Şehir</label>
            <input type="text" id="sehirInput" name="Şehir" placeholder="Örn: İstanbul">
        </div>
        
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn">🚀 Kaydet (POST /users)</button>
            <a href="<?= htmlspecialchars($prefix) ?>/users" class="btn" style="background: var(--text-muted);">İptal</a>
        </div>
    </form>
</div>
