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

## Entity Relationship Diagram

```
┌──────────────────────┐        ┌──────────────────────┐
│       USERS          │        │   ANNOUNCEMENTS      │
├──────────────────────┤        ├──────────────────────┤
│ id (PK)              │        │ id (PK)              │
│ isim                 │        │ title                │
│ soyisim              │        │ content              │
│ bolum                │        │ created_at           │
│ mezuniyet_yili       │        │ updated_at           │
│ sehir                │        └──────────────────────┘
│ pozisyon             │
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
