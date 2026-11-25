# Data Design Documentation (DDD)

## Overview
Bu belge, Alumni Management System'in veri mimarisini, veritabanı şemasını, yetkilendirme ve rol modellerini açıklar. Sistem PostgreSQL 15 veritabanı üzerinde çalışmakta olup, geri dönüşümlü (fallback) JSON desteğini de barındırır.

---

## Authentication & Authorization Architecture

### 1. Client-Server Authentication & Token Flow
Sistemde hem Web oturumu (PHP Session / Cookie) hem de REST API kimlik doğrulama belirteçleri (Bearer Token) desteklenmektedir:

```
┌───────────────────────┐                                     ┌───────────────────────┐
│        CLIENT         │                                     │        SERVER         │
│ (Browser / API Client)│                                     │  (PHP Backend & DB)   │
└───────────┬───────────┘                                     └───────────┬───────────┘
            │                                                             │
            │  1. POST /login { email/username, password }                │
            │────────────────────────────────────────────────────────────>│
            │                                                             │ Verifies credentials
            │                                                             │ Generates session / token
            │  2. Response + Auth Token (or Set-Cookie Session)           │
            │<────────────────────────────────────────────────────────────│
            │                                                             │
            │  3. GET /admin (or /api/...) + Header (Bearer Token/Cookie) │
            │────────────────────────────────────────────────────────────>│
            │                                                             │ AuthMiddleware checks:
            │                                                             │ - Is authenticated?
            │                                                             │ - Has required role?
            │  4. Response (Protected Data or View)                       │
            │<────────────────────────────────────────────────────────────│
            │                                                             │
```

### 2. User Roles & Permission Matrix (RBAC)

Sistemdeki kullanıcılar aşağıdaki rollere sahiptir:

| Rol | Tanım | Yetkiler & Erişim Alanları |
|-----|-------|----------------------------|
| `admin` | Sistem Yöneticisi | - Yönetim Paneli (`/admin`) erişimi ve tam denetim<br>- Kullanıcı listesini görme, rol değiştirme, kullanıcı silme/düzenleme<br>- Tüm iş ilanları, başvurular ve duyuruları yönetme<br>- Sistem istatistiklerini izleme |
| `alumni` | Üniversite Mezunu | - Giriş / Çıkış ve Kişisel Profil (`/profile`) yönetimi<br>- Mezunlar dizinini görüntüleme ve arama<br>- İş ilanlarını inceleme ve yeni iş ilanı oluşturma<br>- İş ilanlarına başvurma ve kendi başvurularını takip etme<br>- Bölüm duyurularını görüntüleme |
| `student` | Üniversite Öğrencisi | - Giriş / Çıkış ve Profil (`/profile`) yönetimi<br>- Mezunlar dizinini ve mezun profillerini inceleme<br>- İş ve staj ilanlarını inceleme, başvuru yapma<br>- Kendi başvurularını görüntüleme |
| `guest` | Ziyaretçi (Giriş Yapmamış) | - Ana sayfa, hakkımızda ve genel mezun/ilan listesini okuma<br>- Sisteme Kayıt Olma (`/register`) ve Giriş Yapma (`/login`) |

---

## Entities (Varlıklar)

### 1. User (Kullanıcı / Mezun)

Kullanıcı ve mezun bilgilerini, kimlik doğrulama ve rol atamalarını tutan ana varlık.

| Alan | Tip | Açıklama | Zorunlu |
|------|-----|----------|---------|
| `id` | INTEGER | Benzersiz kullanıcı kimliği (Primary Key) | ✅ Otomatik |
| `isim` | VARCHAR(100) | Kullanıcının adı | ✅ |
| `soyisim` | VARCHAR(100) | Kullanıcının soyadı | ❌ |
| `email` | VARCHAR(150) | Benzersiz e-posta adresi (Giriş kimliği) | ✅ (Unique) |
| `password_hash` | VARCHAR(255) | Bcrypt ile şifrelenmiş parola | ✅ |
| `role` | VARCHAR(50) | Kullanıcı rolü (`admin`, `alumni`, `student`) | ✅ (Default: 'alumni') |
| `auth_token` | VARCHAR(255) | API Bearer Token / Oturum anahtarı | ❌ |
| `bolum` | VARCHAR(150) | Mezun / Öğrenci olduğu bölüm | ❌ |
| `mezuniyet_yili` | INTEGER | Mezuniyet yılı | ❌ |
| `sehir` | VARCHAR(100) | Yaşadığı şehir | ❌ |
| `pozisyon` | VARCHAR(150) | Mevcut iş unvanı / pozisyon | ❌ |
| `created_at` | TIMESTAMP | Kayıt oluşturulma tarihi | ✅ (Otomatik) |
| `updated_at` | TIMESTAMP | Son güncelleme tarihi | ❌ (Otomatik) |

**Active Database Schema (PostgreSQL):**
```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    isim VARCHAR(100) NOT NULL,
    soyisim VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    password_hash VARCHAR(255),
    role VARCHAR(50) DEFAULT 'alumni',
    auth_token VARCHAR(255),
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
| `created_at` | TIMESTAMP | Oluşturulma tarihi | ✅ (otomatik) |
| `updated_at` | TIMESTAMP | Son güncelleme tarihi | ❌ (otomatik) |

**Active Database Schema (PostgreSQL):**
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

| Alan | Tip | Açıklama | Zorunlu / Kural |
|------|-----|----------|-----------------|
| `id` | INTEGER | Benzersiz iş ilanı kimliği (Primary Key) | ✅ Otomatik |
| `user_id` | INTEGER | İlanı oluşturan kullanıcı (Foreign Key -> users.id) | ❌ Opsiyonel |
| `title` | VARCHAR(120) | İlan başlığı / Pozisyon | ✅ **Zorunlu**, Maksimum 120 karakter |
| `company` | VARCHAR(255) | Şirket adı | ✅ **Zorunlu** |
| `location` | VARCHAR(150) | Çalışma konumu / Şehir | ❌ Opsiyonel |
| `description` | TEXT | İlan detaylı açıklaması | ❌ Opsiyonel |
| `requirements` | TEXT | Aranan nitelikler | ❌ Opsiyonel |
| `salary_range` | VARCHAR(100) | Maaş aralığı | ❌ Opsiyonel |
| `job_type` | VARCHAR(50) | Çalışma biçimi (Tam Zamanlı, Yarı Zamanlı, Staj, Uzaktan) | ❌ Opsiyonel |
| `created_at` | TIMESTAMP | Oluşturulma tarihi | ✅ (otomatik) |
| `updated_at` | TIMESTAMP | Son güncelleme tarihi | ❌ (otomatik) |

**Active Database Schema (PostgreSQL):**
```sql
CREATE TABLE job_postings (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(120) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(150),
    description TEXT,
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
| `user_id` | INTEGER | Başvuran kullanıcı (Foreign Key -> users.id) | ❌ |
| `applicant_name` | VARCHAR(150) | Başvuranın adı soyadı | ✅ |
| `applicant_email` | VARCHAR(150) | İletişim e-posta adresi | ✅ |
| `resume_url` | VARCHAR(255) | Özgeçmiş bağlantısı (CV linki) | ❌ |
| `cover_letter` | TEXT | Ön yazı / Açıklama metni | ❌ |
| `status` | VARCHAR(50) | Başvuru durumu (`Pending`, `Reviewed`, `Accepted`, `Rejected`) | ❌ |
| `created_at` | TIMESTAMP | Başvuru tarihi | ✅ (otomatik) |
| `updated_at` | TIMESTAMP | Son güncelleme tarihi | ❌ (otomatik) |

**Active Database Schema (PostgreSQL):**
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

## Entity Relationship Diagram (ERD)

```
┌────────────────────────┐         1 : N         ┌────────────────────────┐         1 : N         ┌────────────────────────┐
│         USERS          │──────────────────────<│      JOB_POSTINGS      │──────────────────────<│      APPLICATIONS      │
├────────────────────────┤                       ├────────────────────────┤                       ├────────────────────────┤
│ id (PK)                │                       │ id (PK)                │                       │ id (PK)                │
│ isim                   │                       │ user_id (FK)           │                       │ job_posting_id (FK)    │
│ soyisim                │                       │ title                  │                       │ user_id (FK)           │
│ email (UNIQUE)         │                       │ company                │                       │ applicant_name         │
│ password_hash          │                       │ location               │                       │ applicant_email        │
│ role (admin/alumni/...)│                       │ description            │                       │ resume_url             │
│ auth_token             │                       │ requirements           │                       │ cover_letter           │
│ bolum                  │                       │ salary_range           │                       │ status                 │
│ mezuniyet_yili         │                       │ job_type               │                       │ created_at             │
│ sehir                  │                       │ created_at             │                       │ updated_at             │
│ pozisyon               │                       │ updated_at             │                       └────────────────────────┘
│ created_at             │                       └────────────────────────┘                                    │
│ updated_at             │                                    │                                                │
└────────────────────────┘                                    │                                                │
            │                                                 │                                                │
            └─────────────────────────────────────────────────┴────────────────── 1 : N ───────────────────────┘

┌────────────────────────┐
│     ANNOUNCEMENTS      │
├────────────────────────┤
│ id (PK)                │
│ title                  │
│ content                │
│ created_at             │
│ updated_at             │
└────────────────────────┘
```

---

## Active Storage & Architecture Status

| Özellik | Durum / Değer |
|---------|---------------|
| **Birincil Veritabanı** | PostgreSQL 15 (Docker container: `alumni_db`, Port: `5432`) |
| **Yedek / Fallback** | JSON Dosya Depolaması (`/tmp/alumni_data/` & `data/`) |
| **Kimlik Doğrulama** | PHP Session + Secure Cookie + HTTP Bearer Token |
| **Yetkilendirme (RBAC)**| `admin`, `alumni`, `student`, `guest` |
| **Şifreleme Algoritması** | Bcrypt (`PASSWORD_BCRYPT`, salt otomatik) |
