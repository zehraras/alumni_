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

-- Seed job_postings table
INSERT INTO job_postings (user_id, title, company, location, description, requirements, salary_range, job_type, created_at, updated_at) VALUES 
(1, 'Full Stack PHP & Laravel Geliştirici', 'Trendyol', 'İstanbul (Hibrit)', 'Büyüyen e-ticaret altyapımızda yüksek ölçekli servisler geliştirecek deneyimli takım arkadaşı arıyoruz.', 'PHP 8+, PostgreSQL, Docker ve mikroservis mimarisi bilgisi.', '75.000 - 95.000 TL', 'Full-time', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(2, 'Backend Yazılım Mühendisi', 'Getir', 'İstanbul (Uzaktan)', 'Gerçek zamanlı sipariş ve lojistik servisleri için dağıtık backend mimarileri geliştirmek.', 'Go veya PHP deneyimi, Redis ve message broker tecrübesi.', '85.000 - 110.000 TL', 'Remote', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(3, 'Frontend React Geliştirici', 'Hepsiburada', 'İstanbul (Hibrit)', 'Milyonlarca kullanıcıya hizmet veren web arayüzlerinin performansını ve kullanıcı deneyimini artırmak.', 'React, TypeScript, Next.js ve modern CSS araçlarına hakimiyet.', '65.000 - 85.000 TL', 'Hybrid', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(4, 'Yapay Zeka ve NLP Mühendisi', 'Baykar Teknoloji', 'İstanbul (Ofis)', 'Otonom sistemler ve büyük dil modelleri üzerinde Ar-Ge projeleri yürütmek.', 'Python, PyTorch, Derin Öğrenme algoritmaları ve veri işleme deneyimi.', '90.000 - 130.000 TL', 'Full-time', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(5, 'DevOps & Bulut Altyapı Uzmanı', 'Insider', 'İzmir (Uzaktan)', 'AWS ve Kubernetes üzerinde altyapı otomasyonu ve CI/CD hatlarının yönetimi.', 'Docker, Kubernetes, Terraform ve bulut sağlayıcıları tecrübesi.', '80.000 - 115.000 TL', 'Remote', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(6, 'Junior Yazılım Mühendisi (Yeni Mezun)', 'Logo Yazılım', 'Kocaeli (Ofis)', 'Kurumsal ERP projelerinde genç yetenek programı kapsamında yetiştirilmek üzere.', 'Bilgisayar veya Yazılım Mühendisliği mezunu, temel OOP ve SQL bilgisi.', '45.000 - 60.000 TL', 'Full-time', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- Seed applications table
INSERT INTO applications (job_posting_id, user_id, applicant_name, applicant_email, resume_url, cover_letter, status, created_at, updated_at) VALUES 
(1, 2, 'Ahmet Yılmaz', 'ahmet.yilmaz@example.com', 'https://linkedin.com/in/ahmetyilmaz', 'PHP ve veritabanı optimizasyonu konusunda 3 yıllık tecrübem bulunmaktadır.', 'Reviewed', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(1, 3, 'Mehmet Kaya', 'mehmet.kaya@example.com', 'https://github.com/mehmetkaya', 'Full stack projelerdeki portföyüm ektedir.', 'Pending', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(2, 6, 'Can Şahin', 'can.sahin@example.com', 'https://linkedin.com/in/cansahin', 'Dağıtık sistemlere büyük ilgi duyuyorum.', 'Pending', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(4, 1, 'Zehra Aras', 'zehra.aras@example.com', 'https://github.com/zehraras', 'Yapay zeka modelleri ve veri analitiği üzerine tez çalışmam bulunmaktadır.', 'Accepted', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
