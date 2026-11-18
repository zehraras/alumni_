# Design System & UI Specifications (DESIGN.MD)

> Inspired by [refero.design](https://refero.design) & [awesome-design-md](https://github.com/VoltAgent/awesome-design-md)

Bu belge, **Alumni Management System** web arayüzünün tasarım sistemini, bileşen kütüphanesini, düzen kurallarını ve etkileşim standartlarını belirler.

---

## 1. Tasarım Felsefesi (Core Philosophy)

1. **Content-First & Scannable:** Bilgiler kolay taranabilir kartlar, tablolar ve hiyerarşik tipografi ile sunulur.
2. **Minimal & Clean Aesthetic:** Gereksiz süslemeler yerine işlevsel boşluklar (whitespace), yumuşak köşeler ve dengeli gölgeler kullanılır.
3. **Consistent Component Ecosystem:** Tüm sayfalarda aynı buton, kart, rozet ve form dili korunur.
4. **Accessible & Responsive:** Her cihazda (mobil, tablet, masaüstü) esnek ve yüksek kontrastlı (WCAG 2.1 AA) bir deneyim sağlanır.

---

## 2. Spacing & Layout Grid (8pt Scale)

Düzen boşlukları 4 ve 8 pikselin katları olarak yapılandırılmıştır:

| Adı | Boyut (rem/px) | Kullanım Alanı |
|-----|----------------|----------------|
| `space-1` | `0.25rem` (4px) | Rozet içi padding, mikro boşluklar |
| `space-2` | `0.5rem` (8px) | Buton içi dikey boşluk, ikon aralıkları |
| `space-3` | `0.75rem` (12px) | Input içi padding, küçük kart boşluğu |
| `space-4` | `1rem` (16px) | Standart bileşenler arası boşluk |
| `space-6` | `1.5rem` (24px) | Kart içi padding, form grupları |
| `space-8` | `2rem` (32px) | Bölüm ve section boşlukları |
| `space-12` | `3rem` (48px) | Hero banner padding ve sayfa marjinleri |

- **Max Container Width:** `1080px` (Ortalanmış ve iki yandan responsive dolgulu).

---

## 3. UI Bileşen Kütüphanesi (Component Library)

### 3.1. Cards (Konteynerler)
- **Arkaplan:** `#ffffff`
- **Kenarlık:** `1px solid var(--border)` (`#e2e8f0`)
- **Köşe Yuvarlama:** `12px` (Border-radius)
- **Gölge:** `box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);`
- **Hover:** Hafif kalkma efekti (`transform: translateY(-2px); transition: all 0.2s ease;`).

### 3.2. Badges (Durum Rozetleri)
Rozetler `999px` tam oval (pill) yapıda ve açık zemin üzerine koyu yazıyla oluşturulur:
- **Blue Badge (Bilgi/Profil):** Arkaplan `#eff6ff`, Yazı `#1e40af`
- **Green Badge (Aktif/Başarılı):** Arkaplan `#dcfce7`, Yazı `#15803d`
- **Yellow/Amber Badge (Beklemede/Uyarı):** Arkaplan `#fef3c7`, Yazı `#92400e`
- **Red Badge (Hata/Kritik):** Arkaplan `#fee2e2`, Yazı `#b91c1c`

### 3.3. Buttons (Butonlar)
Tüm butonlar `border-radius: 8px`, `font-weight: 500` ve geçiş animasyonuna (`transition: all 0.2s`) sahiptir:
- **Primary:** `background: #2563eb; color: #fff;` -> Hover: `#1d4ed8`
- **Success:** `background: #16a34a; color: #fff;` -> Hover: `#15803d`
- **Warning / Edit:** `background: #eab308; color: #fff;` -> Hover: `#ca8a04`
- **Danger / Delete:** `background: #dc2626; color: #fff;` -> Hover: `#b91c1c`
- **Secondary / Ghost:** `background: #f1f5f9; color: #334155;` -> Hover: `#e2e8f0`

### 3.4. Forms & Inputs (Giriş Alanları)
- **Padding:** `0.75rem 1rem`
- **Border:** `1px solid #cbd5e1`
- **Focus:** `outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);`
- **Labels:** `font-weight: 600; font-size: 0.9rem; color: #334155; margin-bottom: 0.35rem;`

### 3.5. Tables (Veri Tabloları)
- `border-collapse: collapse; width: 100%;`
- Başlık satırı: `#f8fafc` arkaplan, `#64748b` metin, `text-transform: uppercase`, `font-size: 0.75rem`.
- Satır hover: `background-color: #f8fafc`.
- Taşma yönetimi: Mobilde yatay kaydırılabilir (`overflow-x: auto`).

---

## 4. Sayfa Düzenleri ve Sayfa Bazlı Tasarım (Page Layouts)

1. **Header (Global Navbar):**
   - Logo sol tarafta, menü linkleri sağ tarafta.
   - Aktif sayfa linkinde `#eff6ff` zemin ve koyu mavi renk vurgusu.
2. **Hero Section (Ana Sayfa):**
   - `#1e3a8a` - `#2563eb` - `#3b82f6` doğrusal gradient.
   - Doğrudan eyleme geçiren büyük butonlar ve beyaz tipografi.
3. **List Pages (`/users`, `/announcements`, `/job-postings`, `/applications`):**
   - Sayfa üstünde başlık + açıklama + sağda yeşil renkli **"+ Yeni Ekle"** aksiyon butonu.
   - Kart listesi veya zenginleştirilmiş veri tablosu görünümü.
4. **Form Pages (`/create`, `/edit`):**
   - Geri dönüş linki, iki sütunlu dengeli form ızgarası (`flex-gap`), belirgin kaydet butonu.
5. **Detail Pages (`/show`):**
   - Üst bilgi kartı, detay metin alanı ve ilişkili kayıtlar (örneğin iş ilanının altındaki başvuru listesi ve başvuru formu).
6. **Error Page (`/404`):**
   - Büyük 404 tipografisi, koruyucu rozet, hatanın sebebi ve ana sayfa / geri dönüş yönlendirmeleri.

---

## 5. Responsive Breakpointler

```css
/* Mobile */
@media (max-width: 640px) {
    header { flex-direction: column; gap: 1rem; padding: 1rem; }
    nav { flex-wrap: wrap; justify-content: center; }
    main { padding: 1rem; }
    .card { padding: 1.25rem; }
}

/* Tablet & Desktop */
@media (min-width: 641px) {
    main { padding: 2rem; }
}
```
