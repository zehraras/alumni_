<?php

/**
 * JobPostingFactory
 * Generates mock data for job_postings
 */
class JobPostingFactory {
    private static array $titles = [
        'Full Stack PHP Geliştirici',
        'Frontend React / Vue Geliştirici',
        'Backend Yazılım Mühendisi',
        'Yapay Zeka ve Veri Bilimci',
        'DevOps ve Bulut Mühendisi',
        'Mobil Uygulama Geliştirici (Flutter / iOS)',
        'Siber Güvenlik Uzmanı',
        'Yazılım Kalite & Test Mühendisi',
        'Junior Yazılım Geliştirici (Yeni Mezun)',
        'Veritabanı Yöneticisi (PostgreSQL / DBA)'
    ];

    private static array $companies = [
        'Trendyol', 'Getir', 'Hepsiburada', 'Baykar Teknoloji', 
        'Aselsan', 'Logo Yazılım', 'SoftTech', 'Insider', 'Peak Games', 'OBSS Teknoloji'
    ];

    private static array $locations = [
        'İstanbul (Hibrit)', 'Ankara (Ofis)', 'İzmir (Uzaktan)', 
        'İstanbul (Uzaktan)', 'Bursa (Ofis)', 'Eskişehir (Hibrit)', 'Tamamen Uzaktan (Remote)'
    ];

    private static array $descriptions = [
        'Büyüyen yazılım ekibimizde mikroservis mimarisiyle modern web projeleri geliştirecek takım arkadaşı arıyoruz.',
        'Yüksek trafikli e-ticaret ve dağıtık sistemler üzerinde ölçeklenebilir backend servisleri geliştirmek.',
        'Yapay zeka ve büyük veri analitiği projelerinde makine öğrenmesi modelleri kuracak ve üretim ortamına alacak mühendis.',
        'Bulut altyapımızı (Docker, Kubernetes, AWS) yönetecek ve CI/CD süreçlerini otomatize edecek DevOps uzmanı.',
        'Kullanıcı dostu, modern ve performanslı arayüzler inşa edecek deneyimli frontend geliştirici.'
    ];

    private static array $salaries = [
        '45.000 - 60.000 TL', '60.000 - 80.000 TL', '80.000 - 110.000 TL', 
        '110.000 - 150.000 TL', 'Performansa Dayalı / Görüşülecek'
    ];

    private static array $jobTypes = [
        'Full-time', 'Full-time', 'Remote', 'Hybrid', 'Part-time', 'Internship'
    ];

    public static function create(array $overrides = []): array {
        $title = self::$titles[array_rand(self::$titles)];
        $company = self::$companies[array_rand(self::$companies)];
        $location = self::$locations[array_rand(self::$locations)];
        $description = self::$descriptions[array_rand(self::$descriptions)];
        $salary = self::$salaries[array_rand(self::$salaries)];
        $type = self::$jobTypes[array_rand(self::$jobTypes)];
        $userId = rand(1, 10);

        return array_merge([
            'user_id' => $userId,
            'title' => $title,
            'company' => $company,
            'location' => $location,
            'description' => $description,
            'requirements' => 'İlgili mühendislik bölümlerinden mezun, ekip çalışmasına yatkın, Git ve modern teknolojilere hakim.',
            'salary_range' => $salary,
            'job_type' => $type,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ], $overrides);
    }

    public static function count(int $count): array {
        $records = [];
        for ($i = 0; $i < $count; $i++) {
            $records[] = self::create();
        }
        return $records;
    }
}
