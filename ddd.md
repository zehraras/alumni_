# Data Design Documentation (DDD)

## Overview
Bu belge, Alumni Management System'in veri yapısını ve veritabanı tasarımını açıklar. Şu an veriler JSON dosyalarında tutulmaktadır (geçici). İleride PostgreSQL veritabanına geçiş yapılacaktır.

---

## Entities (Varlıklar)

### 1. User (Kullanıcı / Mezun)

Mezun bilgilerini tutan ana varlık.

| Alan | Tip | Açıklama | Zorunlu |
|------|-----|----------|---------|
| `id` | INTEGER | Benzersiz kullanıcı kimliği (Primary Key) | ✅ |
| `İsim` | VARCHAR(100) | Mezunun adı | ✅ |
| `Soyisim` | VARCHAR(100) | Mezunun soyadı | ❌ |
| `Bölüm` | VARCHAR(150) | Mezun olduğu bölüm | ❌ |
| `MezuniyetYılı` | INTEGER | Mezuniyet yılı | ❌ |
| `Şehir` | VARCHAR(100) | Yaşadığı şehir | ❌ |
| `Pozisyon` | VARCHAR(150) | Mevcut iş pozisyonu | ❌ |
| `createdAt` | TIMESTAMP | Kayıt oluşturulma tarihi | ✅ (otomatik) |
| `updatedAt` | TIMESTAMP | Son güncelleme tarihi | ❌ (otomatik) |

**Planned SQL:**
```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    isim VARCHAR(100) NOT NULL,
    soyisim VARCHAR(100),
    bolum VARCHAR(150),
    mezuniyet_yili INTEGER,
    sehir VARCHAR(100),
    pozisyon VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

### 2. Announcement (Duyuru)

Bölüm duyurularını tutan varlık.

| Alan | Tip | Açıklama | Zorunlu |
|------|-----|----------|---------|
| `id` | INTEGER | Benzersiz duyuru kimliği (Primary Key) | ✅ |
| `title` | VARCHAR(255) | Duyuru başlığı | ✅ |
| `content` | TEXT | Duyuru içeriği | ✅ |
| `createdAt` | TIMESTAMP | Oluşturulma tarihi | ✅ (otomatik) |
| `updatedAt` | TIMESTAMP | Son güncelleme tarihi | ❌ (otomatik) |

**Planned SQL:**
```sql
CREATE TABLE announcements (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

### 3. Job Posting (İş İlanı)

Mezunların veya bölümün paylaştığı iş ve staj ilanlarını tutan varlık. `users` tablosu ile ilişkilidir (`user_id`).

| Alan | Tip | Açıklama | Zorunlu |
|------|-----|----------|---------|
| `id` | INTEGER | Benzersiz iş ilanı kimliği (Primary Key) | ✅ |
| `user_id` | INTEGER | İlanı oluşturan mezun (Foreign Key -> users.id) | ❌ |
| `title` | VARCHAR(255) | İlan başlığı / Pozisyon | ✅ |
| `company` | VARCHAR(255) | Şirket adı | ✅ |
| `location` | VARCHAR(150) | Çalışma konumu / Şehir | ❌ |
| `description` | TEXT | İlan detaylı açıklaması | ✅ |
| `requirements` | TEXT | Aranan nitelikler | ❌ |
| `salary_range` | VARCHAR(100) | Maaş aralığı | ❌ |
| `job_type` | VARCHAR(50) | Çalışma biçimi (Tam Zamanlı, Yarı Zamanlı, Staj, Uzaktan) | ❌ |
| `created_at` | TIMESTAMP | Oluşturulma tarihi | ✅ (otomatik) |
| `updated_at` | TIMESTAMP | Son güncelleme tarihi | ❌ (otomatik) |

**Planned SQL:**
```sql
CREATE TABLE job_postings (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(150),
    description TEXT NOT NULL,
    requirements TEXT,
    salary_range VARCHAR(100),
    job_type VARCHAR(50) DEFAULT 'Full-time',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

### 4. Application (İş Başvurusu)

Bir mezunun veya adayın belirli bir iş ilanına yaptığı başvuruyu temsil eden varlık. `job_postings` tablosuna ve opsiyonel olarak `users` tablosuna bağlıdır.

| Alan | Tip | Açıklama | Zorunlu |
|------|-----|----------|---------|
| `id` | INTEGER | Benzersiz başvuru kimliği (Primary Key) | ✅ |
| `job_posting_id` | INTEGER | Başvurulan iş ilanı (Foreign Key -> job_postings.id) | ✅ |
| `user_id` | INTEGER | Başvuran mezun / kullanıcı (Foreign Key -> users.id) | ❌ |
| `applicant_name` | VARCHAR(150) | Başvuranın adı soyadı | ✅ |
| `applicant_email` | VARCHAR(150) | İletişim e-posta adresi | ✅ |
| `resume_url` | VARCHAR(255) | Özgeçmiş bağlantısı (CV linki) | ❌ |
| `cover_letter` | TEXT | Ön yazı / Açıklama metni | ❌ |
| `status` | VARCHAR(50) | Başvuru durumu (Pending, Reviewed, Accepted, Rejected) | ❌ |
| `created_at` | TIMESTAMP | Başvuru tarihi | ✅ (otomatik) |
| `updated_at` | TIMESTAMP | Son güncelleme tarihi | ❌ (otomatik) |

**Planned SQL:**
```sql
CREATE TABLE applications (
    id SERIAL PRIMARY KEY,
    job_posting_id INTEGER NOT NULL REFERENCES job_postings(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    applicant_name VARCHAR(150) NOT NULL,
    applicant_email VARCHAR(150) NOT NULL,
    resume_url VARCHAR(255),
    cover_letter TEXT,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## Entity Relationship Diagram

```
┌──────────────────────┐         1 : N         ┌──────────────────────┐         1 : N         ┌──────────────────────┐
│       USERS          │──────────────────────<│    JOB_POSTINGS      │──────────────────────<│    APPLICATIONS      │
├──────────────────────┤                       ├──────────────────────┤                       ├──────────────────────┤
│ id (PK)              │                       │ id (PK)              │                       │ id (PK)              │
│ isim                 │                       │ user_id (FK)         │                       │ job_posting_id (FK)  │
│ soyisim              │                       │ title                │                       │ user_id (FK)         │
│ bolum                │                       │ company              │                       │ applicant_name       │
│ mezuniyet_yili       │                       │ location             │                       │ applicant_email      │
│ sehir                │                       │ description          │                       │ resume_url           │
│ pozisyon             │                       │ requirements         │                       │ cover_letter         │
│ created_at           │                       │ salary_range         │                       │ status               │
│ updated_at           │                       │ job_type             │                       │ created_at           │
└──────────────────────┘                       │ created_at           │                       │ updated_at           │
          │                                    │ updated_at           │                       └──────────────────────┘
          │                                    └──────────────────────┘                                   │
          │                                                                                               │
          └─────────────────────────────────────────────── 1 : N ─────────────────────────────────────────┘

┌──────────────────────┐
│   ANNOUNCEMENTS      │
├──────────────────────┤
│ id (PK)              │
│ title                │
│ content              │
│ created_at           │
│ updated_at           │
└──────────────────────┘
```

---

## Current Storage (Mevcut Depolama)

| Özellik | Değer |
|---------|-------|
| **Yöntem** | JSON dosyaları (`/tmp/alumni_data/`) |
| **Kalıcılık** | ❌ Geçici — `docker compose down` ile silinir (Forgetting Test) |
| **Veritabanı** | PostgreSQL 15 (container hazır, henüz bağlanmadı) |

## Future Plan (Gelecek Plan)

- [ ] PostgreSQL bağlantısı kurulacak (PDO)
- [ ] SQL tabloları oluşturulacak (`CREATE TABLE`)
- [ ] Model sınıfları JSON yerine SQL sorguları kullanacak
- [ ] Veriler kalıcı hale gelecek (Forgetting Test geçilecek)
