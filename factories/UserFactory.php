<?php
/**
 * UserFactory
 * Generates mock data for users / alumni
 */
class UserFactory {
    private static array $firstNames = [
        'Zehra', 'Ahmet', 'Mehmet', 'Ayşe', 'Fatma', 
        'Can', 'Dilara', 'Burak', 'Zeynep', 'Emre', 
        'Sema', 'Elif', 'Oğuz', 'Ece', 'Murat'
    ];

    private static array $lastNames = [
        'Aras', 'Yılmaz', 'Kaya', 'Demir', 'Çelik', 
        'Şahin', 'Yıldız', 'Öztürk', 'Aydın', 'Özdemir',
        'Aksoy', 'Polat', 'Koç', 'Aslan', 'Tekin'
    ];

    private static array $departments = [
        'Bilgisayar Mühendisliği',
        'Yazılım Mühendisliği',
        'Yönetim Bilişim Sistemleri',
        'Endüstri Mühendisliği',
        'Yapay Zeka Mühendisliği',
        'Elektrik-Elektronik Mühendisliği'
    ];

    private static array $cities = [
        'İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya', 'Eskişehir', 'Kocaeli'
    ];

    private static array $positions = [
        'Yazılım Geliştirici',
        'Frontend Geliştirici',
        'Backend Geliştirici',
        'Full Stack Mühendisi',
        'Veri Analisti',
        'DevOps Mühendisi',
        'Proje Yöneticisi',
        'Sistem Mimarı'
    ];

    /**
     * Generate a single fake user record
     */
    public static function create(array $overrides = []): array {
        $isim = self::$firstNames[array_rand(self::$firstNames)];
        $soyisim = self::$lastNames[array_rand(self::$lastNames)];
        $bolum = self::$departments[array_rand(self::$departments)];
        $mezuniyet_yili = rand(2018, 2025);
        $sehir = self::$cities[array_rand(self::$cities)];
        $pozisyon = self::$positions[array_rand(self::$positions)];

        return array_merge([
            'isim' => $isim,
            'soyisim' => $soyisim,
            'bolum' => $bolum,
            'mezuniyet_yili' => $mezuniyet_yili,
            'sehir' => $sehir,
            'pozisyon' => $pozisyon,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ], $overrides);
    }

    /**
     * Generate multiple fake user records
     */
    public static function count(int $count): array {
        $records = [];
        for ($i = 0; $i < $count; $i++) {
            $records[] = self::create();
        }
        return $records;
    }
}
