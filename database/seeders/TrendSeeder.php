<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trend;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class TrendSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel trends (Gunakan truncate untuk PostgreSQL)
        Trend::truncate();

        // 2. Path ke file JSON hasil scraping
        $jsonPath = storage_path('app/trends.json');

        if (File::exists($jsonPath)) {
            $jsonContent = File::get($jsonPath);
            $trendsData = json_decode($jsonContent, true);

            if ($trendsData) {
                // Konfigurasi Keyword Mapping untuk Kategorisasi
                $mapping = [
                    'Teknologi'  => ['ai', 'tech', 'iphone', 'apple', 'samsung', 'gadget', 'crypto', 'bitcoin', 'software', 'chatgpt', 'deepseek', 'coding', 'digital', 'robot', 'ps5', 'windows', 'ios', 'android'],
                    'Hiburan'    => ['taylor', 'concert', 'bts', 'movie', 'film', 'artis', 'kpop', 'nct', 'netflix', 'konser', 'musik', 'album', 'trailer', 'seleb', 'drama', 'bioskop', 'vlog', 'youtube', 'tiktok'],
                    'Olahraga'   => ['bola', 'liga', 'match', 'fc', 'united', 'madrid', 'timnas', 'skor', 'badminton', 'motogp', 'pssi', 'champion', 'f1', 'atlet', 'juara', 'persib', 'persija', 'voley', 'basket'],
                    'Politik'    => ['ikn', 'presiden', 'menteri', 'dpr', 'pemilu', 'pilkada', 'kpk', 'hukum', 'rakyat', 'politik', 'pemerintah', 'asn', 'negara', 'demokrasi', 'uud', 'sidang', 'partai', 'prajurit'],
                    'Gaya Hidup' => ['diet', 'fashion', 'skincare', 'minimalis', 'kuliner', 'travel', 'wisata', 'kopi', 'masak', 'gaya', 'hidup', 'lifestyle', 'sehat', 'belanja', 'outfit', 'parfum'],
                ];

                $this->command->info("Memproses " . count($trendsData) . " data tren ke PostgreSQL...");

                foreach ($trendsData as $item) {
                    $title = $item['name'] ?? 'Tanpa Judul';
                    
                    // Logika Kategorisasi Otomatis
                    $finalCategory = 'Umum';
                    $loweredTitle = strtolower($title);

                    foreach ($mapping as $category => $keywords) {
                        foreach ($keywords as $keyword) {
                            if (str_contains($loweredTitle, $keyword)) {
                                $finalCategory = $category;
                                break 2;
                            }
                        }
                    }

                    // Logika Dynamic Summary berdasarkan Kategori
                    $summaryTemplate = [
                        'Teknologi'  => "Diskusi seputar inovasi $title sedang ramai diperbincangkan di komunitas teknologi dan digital.",
                        'Hiburan'    => "Topik $title menjadi pusat perhatian para penggemar hiburan dan menjadi perbincangan hangat di budaya populer.",
                        'Olahraga'   => "Update terbaru mengenai $title tengah menjadi sorotan utama bagi para pecinta olahraga di media sosial.",
                        'Politik'    => "Dinamika terkait $title sedang memicu berbagai reaksi dan diskusi intensif mengenai isu publik dan kenegaraan.",
                        'Gaya Hidup' => "Tren gaya hidup dan inspirasi harian mengenai $title sedang banyak dibagikan oleh pengguna hari ini.",
                        'Umum'       => "Topik $title tengah viral dan menduduki daftar tren populer yang paling banyak dibicarakan di platform X."
                    ];

                    $dynamicSummary = $summaryTemplate[$finalCategory] ?? $summaryTemplate['Umum'];

                    // Penanganan jumlah postingan
                    $postCount = $item['tweet_count'] ?? 'Jumlah tidak tersedia';
                    if ($postCount == "N/A" || empty($postCount)) {
                        $postCount = 'Jumlah tidak tersedia';
                    }

                    // Buat data ke database
                    Trend::create([
                        'title'      => $title,
                        'category'   => $finalCategory,
                        'post_count' => $postCount,
                        'summary'    => $dynamicSummary,
                        // Kirim sebagai ARRAY (Karena model Trend menggunakan $casts array)
                        'news_links' => [
                            [
                                'title' => 'Cari Berita di Google News', 
                                'url'   => 'https://www.google.com/search?q=' . urlencode($title . ' news')
                            ],
                            [
                                'title' => 'Lihat di Platform X', 
                                'url'   => 'https://x.com/search?q=' . urlencode($title)
                            ]
                        ],
                        // Menggunakan waktu modifikasi file JSON agar akurat
                        'fetched_at' => Carbon::createFromTimestamp(File::lastModified($jsonPath))
                                        ->translatedFormat('d F Y, H:i') . ' WIB'
                    ]);
                }
                $this->command->info("🚀 Seeding Sukses!");
            }
        } else {
            $this->command->warn("File JSON tidak ditemukan di $jsonPath. Pastikan scraper.py berhasil dijalankan.");
        }
    }
}