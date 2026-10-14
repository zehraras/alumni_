<?php
/**
 * AnnouncementFactory
 * Generates mock data for announcements
 */
class AnnouncementFactory {
    private static array $samples = [
        [
            'title' => '2026 Mezunlar Buluşması ve Gala Gecesi',
            'content' => 'Geleneksel mezunlar buluşmamız bu yıl kampüs ana bahçesinde gerçekleştirilecektir. Tüm mezunlarımız davetlidir.'
        ],
        [
            'title' => 'Yapay Zeka ve Yazılım Kariyer Günleri',
            'content' => 'Sektörün önde gelen teknoloji firmalarının katılımıyla kariyer günleri etkinliği düzenlenecektir. CV hazırlamayı unutmayın.'
        ],
        [
            'title' => 'Mezun-Öğrenci Mentorluk Programı Başvuruları Açıldı',
            'content' => 'Mezunlarımızın deneyimlerini yeni öğrencilerimizle paylaşacağı mentorluk programı yeni dönem kayıtları başlamıştır.'
        ],
        [
            'title' => 'Teknopark Staj ve İş Fırsatları',
            'content' => 'Teknopark bünyesindeki Ar-Ge şirketleri yeni mezun yazılımcı ve mühendis arayışındadır. İlan detayları kariyer portalında.'
        ],
        [
            'title' => 'Yıllık Mezunlar Derneği Genel Kurulu',
            'content' => 'Mezunlar Derneği yıllık olağan genel kurul toplantısı cumartesi günü saat 14:00\'te rektörlük konferans salonunda yapılacaktır.'
        ]
    ];

    /**
     * Generate a single fake announcement record
     */
    public static function create(array $overrides = []): array {
        $sample = self::$samples[array_rand(self::$samples)];
        return array_merge([
            'title' => $sample['title'],
            'content' => $sample['content'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ], $overrides);
    }

    /**
     * Generate multiple fake announcement records
     */
    public static function count(int $count): array {
        $records = [];
        $total = count(self::$samples);
        for ($i = 0; $i < $count; $i++) {
            $sample = self::$samples[$i % $total];
            $records[] = [
                'title' => $sample['title'],
                'content' => $sample['content'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        return $records;
    }
}
