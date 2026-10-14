-- Database Seed Script: seeders/seed.sql

-- Seed users table
INSERT INTO users (isim, soyisim, bolum, mezuniyet_yili, sehir, pozisyon, created_at, updated_at) VALUES 
('Zehra', 'Aras', 'Yapay Zeka Mühendisliği', 2024, 'İstanbul', 'Yazılım Mühendisi', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Ahmet', 'Yılmaz', 'Bilgisayar Mühendisliği', 2022, 'Ankara', 'Backend Geliştirici', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Mehmet', 'Kaya', 'Yazılım Mühendisliği', 2023, 'İzmir', 'Full Stack Mühendisi', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Ayşe', 'Demir', 'Yönetim Bilişim Sistemleri', 2021, 'Bursa', 'Veri Analisti', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Fatma', 'Çelik', 'Endüstri Mühendisliği', 2020, 'Antalya', 'Proje Yöneticisi', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Can', 'Şahin', 'Bilgisayar Mühendisliği', 2025, 'Eskişehir', 'Frontend Geliştirici', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Dilara', 'Yıldız', 'Yazılım Mühendisliği', 2024, 'İstanbul', 'DevOps Mühendisi', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Burak', 'Öztürk', 'Elektrik-Elektronik Mühendisliği', 2019, 'Kocaeli', 'Sistem Mimarı', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Zeynep', 'Aydın', 'Yönetim Bilişim Sistemleri', 2023, 'Ankara', 'İş Analisti', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Emre', 'Özdemir', 'Bilgisayar Mühendisliği', 2022, 'İzmir', 'Mobil Geliştirici', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- Seed announcements table
INSERT INTO announcements (title, content, created_at, updated_at) VALUES 
('2026 Mezunlar Buluşması ve Gala Gecesi', 'Geleneksel mezunlar buluşmamız bu yıl kampüs ana bahçesinde gerçekleştirilecektir. Tüm mezunlarımız davetlidir.', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Yapay Zeka ve Yazılım Kariyer Günleri', 'Sektörün önde gelen teknoloji firmalarının katılımıyla kariyer günleri etkinliği düzenlenecektir. CV hazırlamayı unutmayın.', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Mezun-Öğrenci Mentorluk Programı Başvuruları Açıldı', 'Mezunlarımızın deneyimlerini yeni öğrencilerimizle paylaşacağı mentorluk programı yeni dönem kayıtları başlamıştır.', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Teknopark Staj ve İş Fırsatları', 'Teknopark bünyesindeki Ar-Ge şirketleri yeni mezun yazılımcı ve mühendis arayışındadır. İlan detayları kariyer portalında.', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Yıllık Mezunlar Derneği Genel Kurulu', 'Mezunlar Derneği yıllık olağan genel kurul toplantısı cumartesi günü saat 14:00''te rektörlük konferans salonunda yapılacaktır.', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
