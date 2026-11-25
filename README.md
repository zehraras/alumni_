# Alumni System

A modern Alumni Management and Tracking System designed to manage graduate information, facilitate networking, and keep alumni connected with their institution.

## System Overview & Features
- **Alumni Management:** Directory and profiles for university graduates.
- **Networking & Communications:** Connection channels and event management.
- **Data Persistence:** Relational database storage with full integrity.

## Tech Stack
- **Back-end:** PHP (Laravel)
- **Database:** PostgreSQL
- **AI Assistant:** Antigravity

## How to Run the Project

This system strictly adheres to single-command containerized deployment via Docker.

### Prerequisites
- Docker & Docker Desktop installed.

### Quick Start
Run the following command in the root directory to build and start all required services:

```bash
docker compose up -d --build
```
## MVC Architecture
This application follows the Model-View-Controller (MVC) architectural pattern to separate concerns:

- **`models/` (Model):** Handles data logic and data representation. E.g., `UserModel` manages reading and writing user data (currently from `data/users.json`).
- **`controllers/` (Controller):** Handles incoming requests, processes user input, interacts with the Model, and returns the appropriate response (JSON or HTML). E.g., `UserController` for web views, `ApiUserController` for API endpoints.
- **`views/` (View):** Contains the HTML templates and UI components to present data to the user.
- **`index.php` (Router):** The entry point of the application that routes incoming HTTP requests to the appropriate controllers.

## Documentation & Architecture Guides
- 📄 **Data Design Documentation:** 👉 **[ddd.md](ddd.md)** (Varlıklar, SQL şemaları, ERD)
- 🎨 **Brand Identity & Guidelines:** 👉 **[brand.md](brand.md)** (Marka kimliği, misyon, renk paleti, tipografi, ton)
- 📐 **Design System & UI Specs:** 👉 **[DESIGN.MD](DESIGN.MD)** (Refero.design & Awesome-design-md tabanlı UI kütüphanesi ve grid)
- 🤖 **AI Assistant Usage:** 👉 **[AGENTS.md](AGENTS.md)** (Antigravity asistan kullanım kayıtları)


## Database Migrations
Migrations are located in `migrations/` and manage the database schema for PostgreSQL:
- `migrations/001_create_users_table.sql`: Schema for `users` table.
- `migrations/002_create_announcements_table.sql`: Schema for `announcements` table.

To run migrations:
```bash
./migrate.sh
```
or with PHP:
```bash
php migrate.php
```

## Database Factories & Seeders
Factories and seeders populate the database with realistic sample data:
- **Factories (`factories/`):**
  - `factories/UserFactory.php`: Generates fake alumni profiles.
  - `factories/AnnouncementFactory.php`: Generates fake department announcements.
  - `factories/JobPostingFactory.php`: Generates fake job postings.
- **Seeders (`seeders/`):**
  - `seeders/UserSeeder.php`: Seeds 10 user records.
  - `seeders/AnnouncementSeeder.php`: Seeds 5 announcement records.
  - `seeders/JobPostingSeeder.php`: Seeds job postings.
  - `seeders/DatabaseSeeder.php`: Master seeder.
  - `seeders/seed.sql`: Direct SQL seed queries for all tables.

To run seeders:
```bash
./seed.sh
```
or with PHP:
```bash
php seed.php
```

## API Routes & Direct / Nested Endpoints

### Direct Routes
- `GET/POST /api/users` & `GET/PUT/PATCH/DELETE /api/users/{id}`
- `GET/POST /api/announcements` & `GET/PUT/PATCH/DELETE /api/announcements/{id}`
- `GET/POST /api/job-postings` & `GET/PUT/PATCH/DELETE /api/job-postings/{id}`
- `GET/POST /api/applications` & `GET/PUT/PATCH/DELETE /api/applications/{id}`

### Nested Routes (Resource Relationships)
- `GET /api/users/{id}/job-postings`: Belirtilen mezunun oluşturduğu iş ilanlarını listeler.
- `POST /api/users/{id}/job-postings`: Belirtilen mezun adına yeni bir iş ilanı ekler.
- `GET /api/job-postings/{id}/applications`: Belirli bir iş ilanına yapılan başvuruları listeler.
- `POST /api/job-postings/{id}/applications`: Belirli bir iş ilanına doğrudan başvuru yapar.
- `GET /api/users/{id}/applications`: Bir mezunun/kullanıcının yaptığı tüm başvuruları listeler.

### Web UIs
- `/users`: Mezun listesi ve yönetimi
- `/announcements`: Bölüm duyuruları
- `/job-postings`: İş ve staj ilanları (ilan oluşturma & başvuru formu ile)
- `/applications`: Gelen iş başvuruları listesi ve detayları

## Middlewares & Validation Guardians

### 1. Request Validation (Postman Invalid Requests)
API isteklerinde gelen veriler katı kurallarla doğrulanır:
- **`JobPosting` Kuralları:**
  - `title`: Zorunlu (required), maksimum 120 karakter (max 120 chars).
  - `company`: Zorunlu (required).
  - `location` (city): Opsiyonel (optional).
  - `description`: Opsiyonel (optional).
  - İhlal durumunda HTTP `422 Unprocessable Entity` durum koduyla detaylı hata döner.

### 2. Logger Middleware (`middlewares/LoggerMiddleware.php`)
Tüm HTTP isteklerini gerçek zamanlı olarak terminale loglar.
- **Format:** `time method route res.type duration`
- **Örnek Çıktı:**
  ```text
  [10:20:30] POST   /api/job-postings              201 18ms
  [10:21:15] GET    /api/users                     200 12ms
  ```

### 3. 404 Guardian Middleware (`middlewares/GuardianMiddleware.php`)
- Geçersiz veya bulunamayan bir uç noktaya yapılan isteklerde `404 Not Found` yanıtı ve detaylı bir açıklama (`reason`) döner.
- **API İstekleri:** JSON formatında hata ve sebep nesnesi:
  ```json
  {
      "status": 404,
      "error": "Not Found",
      "reason": "The requested route '/api/invalid' does not exist.",
      "path": "/api/invalid"
  }
  ```
- **Web İstekleri:** Kullanıcı dostu şık Generic 404 HTML hata sayfası (`views/errors/404.php`).

### 4. 5xx Guardian Middleware
- Beklenmedik sunucu hatalarını (Unhandled Exceptions & Fatal Errors) yakalar ve kullanıcıya güvenli `500 Internal Server Error` yanıtı döner.
- Hatanın sistem çökmelerine yol açmasını engeller.

### 5. Error Logs (`logs/error.logs`)
- Tüm 404 rota hataları ve 5xx sunucu hataları zaman damgası, istek metodu, IP ve dosya/satır bilgisiyle birlikte `logs/error.logs` dosyasına kalıcı olarak kaydedilir.

---

## Authentication & Role-Based Authorization (RBAC)

Sistem; Web oturumu (PHP Session / Cookie) ve REST API belirteçleri (Bearer Token) ile tam çift yönlü kimlik doğrulama ve rol bazlı erişim denetimi (RBAC) sunar.

### 1. Kullanıcı Rolleri & Yetki Matrisi

| Rol | Tanım | Yetkiler & Sayfa Erişimleri |
|-----|-------|-----------------------------|
| `admin` | Sistem Yöneticisi | **Yönetim Paneli (`/admin`)** tam erişim, kullanıcı rolleri değiştirme, kullanıcı silme/düzenleme, sistem istatistikleri, tüm ilan ve duyuru yönetimi. |
| `alumni` | Üniversite Mezunu | **Profilim (`/profile`)**, kendi iş başvurularını izleme, ilan oluşturma, iş ilanlarına başvurma, mezunlar dizinini görüntüleme. |
| `student` | Öğrenci | **Profilim (`/profile`)**, staj ve iş ilanlarını inceleme, başvuru yapma, başvuru durumlarını takip etme. |
| `guest` | Ziyaretçi | Ana sayfa, hakkımızda, kayıt olma (`/register`) ve giriş yapma (`/login`). |

### 2. Kimlik Doğrulama Uç Noktaları

#### Web Arayüzü Rotaları
- `GET /register` & `POST /register`: Yeni mezun / öğrenci kayıt sayfası ve form işlemi.
- `GET /login` & `POST /login`: Giriş sayfası, şifre doğrulama (`password_verify`) ve oturum başlatma.
- `GET /logout`: Güvenli oturum kapatma ve çerez temizleme.
- `GET /profile` & `POST /profile/update`: Kullanıcıya özel profil sayfası, başvuru takibi ve bilgi güncelleme.
- `GET /admin`: Yönetici paneli, sistem istatistikleri ve kullanıcı rol manipülasyonu.
- `POST /admin/users/role`: Kullanıcı rolü değiştirme (`admin`, `alumni`, `student`).
- `POST /admin/users/delete`: Kullanıcı silme işlemi.

#### REST API Rotaları
- `POST /api/auth/register`: JSON formatında yeni üyelik oluşturma.
- `POST /api/auth/login`: E-posta ve şifre ile giriş yaparak API `token` elde etme.
- `POST /api/auth/logout`: Aktif API tokenını sonlandırma.
- `GET /api/auth/me`: `Authorization: Bearer <token>` başlığıyla giriş yapan kullanıcının profilini getirme.

### 3. Auth Middleware (`middlewares/AuthMiddleware.php`)
- **`AuthMiddleware::requireAuth()`**: Yetkisiz web isteklerini `/login` sayfasına yönlendirir; API isteklerinde `401 Unauthorized` JSON yanıtı döner.
- **`AuthMiddleware::requireRole('admin')`**: Yönetici olmayan kullanıcıların `/admin` paneline erişimini engelleyerek `403 Forbidden` veya güvenli yönlendirme uygular.
- **Dinamik Navigasyon Menüsü**: Kullanıcının oturum durumuna ve rolüne göre (`Guest`, `Alumni`, `Admin`) menü ve butonları dinamik olarak render eder.

### 4. Test Hesapları ve Giriş Bilgileri

| Hesap Türü | E-posta | Şifre | Rol |
|------------|---------|-------|-----|
| **Yönetici (Zehra Aras)** | `zehra@alumni.edu` | `zehra123` | `admin` |
| **Sistem Yöneticisi** | `admin@alumni.edu` | `admin123` | `admin` |
| **Mezun Üye** | `ahmet.2@alumni.edu` | `password123` | `alumni` |





