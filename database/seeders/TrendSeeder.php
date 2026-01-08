<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trend;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class TrendSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan tabel trends ada sebelum melakukan operasi
        if (!Schema::hasTable('trends')) {
            $this->command->error("Tabel 'trends' tidak ditemukan. Pastikan migrasi sudah dijalankan.");
            return;
        }

        // 2. Kosongkan tabel trends untuk menghindari data ganda (Truncate aman untuk PostgreSQL)
        Trend::truncate();

        // 3. Path ke file JSON hasil scraping
        $jsonPath = storage_path('app/trends.json');

        if (File::exists($jsonPath)) {
            $jsonContent = File::get($jsonPath);
            $trendsData = json_decode($jsonContent, true);

            if (!empty($trendsData)) {
                // Konfigurasi Keyword Mapping untuk Kategorisasi Otomatis yang lebih spesifik
                $mapping = [
                    'Teknologi'  => ['ai', 'tech', 'iphone', 'apple', 'samsung', 'gadget', 'crypto', 'bitcoin', 'software', 'chatgpt', 'deepseek', 'coding', 'digital', 'robot', 'chain'],
                    'Hiburan'    => ['taylor', 'concert', 'bts', 'movie', 'film', 'artis', 'kpop', 'nct', 'netflix', 'konser', 'musik', 'album', 'trailer', 'seleb', 'drama', 'standup', 'komedi', 'bully'],
                    'Olahraga'   => ['bola', 'liga', 'match', 'fc', 'united', 'madrid', 'timnas', 'skor', 'badminton', 'motogp', 'pssi', 'champion', 'f1', 'atlet', 'juara', 'persib', 'persija'],
                    'Politik'    => ['ikn', 'presiden', 'menteri', 'dpr', 'dprd', 'pemilu', 'pilkada', 'kpk', 'hukum', 'rakyat', 'politik', 'pemerintah', 'asn', 'negara', 'demokrasi', 'kabinet', 'prabowo', 'gibran', 'nusron'],
                    'Gaya Hidup' => ['diet', 'fashion', 'skincare', 'minimalis', 'kuliner', 'travel', 'wisata', 'kopi', 'masak', 'lifestyle', 'sehat', 'belanja'],
                ];

                $this->command->info("Memproses " . count($trendsData) . " data tren ke PostgreSQL...");

                foreach ($trendsData as $item) {
                    $title = $item['name'] ?? 'Tanpa Judul';
                    
                    // Logika Kategorisasi: Gunakan domainContext dari JSON sebagai dasar, 
                    // tapi coba cari kategori yang lebih spesifik jika domainContext adalah "Umum"
                    $finalCategory = $item['domainContext'] ?? 'Umum';
                    $loweredTitle = strtolower($title);

                    if ($finalCategory === 'Umum') {
                        foreach ($mapping as $category => $keywords) {
                            foreach ($keywords as $keyword) {
                                if (str_contains($loweredTitle, $keyword)) {
                                    $finalCategory = $category;
                                    break 2;
                                }
                            }
                        }
                    }

                    // Logika Dynamic Summary berdasarkan Kategori
                    $summaryTemplate = [
                        'Teknologi'  => "Diskusi seputar inovasi '$title' sedang ramai diperbincangkan di komunitas teknologi dan digital.",
                        'Hiburan'    => "Topik '$title' menjadi pusat perhatian para penggemar hiburan dan menjadi perbincangan hangat di budaya populer.",
                        'Olahraga'   => "Update terbaru mengenai '$title' tengah menjadi sorotan utama bagi para pecinta olahraga di media sosial.",
                        'Politik'    => "Dinamika terkait '$title' sedang memicu berbagai reaksi dan diskusi intensif mengenai isu publik dan kenegaraan.",
                        'Gaya Hidup' => "Tren gaya hidup dan inspirasi harian mengenai '$title' sedang banyak dibagikan oleh pengguna hari ini.",
                        'Umum'       => "Topik '$title' tengah viral dan menduduki daftar tren populer yang paling banyak dibicarakan di platform X."
                    ];

                    $dynamicSummary = $summaryTemplate[$finalCategory] ?? $summaryTemplate['Umum'];

                    // Penanganan jumlah postingan (Handle string kosong dari JSON)
                    $postCount = $item['tweet_count'] ?? '';
                    if (empty($postCount) || $postCount == "N/A") {
                        $postCount = '';
                    }

                    // Buat data ke database
                    Trend::create([
                        'title'      => $title,
                        'category'   => $finalCategory,
                        'post_count' => $postCount,
                        'summary'    => $dynamicSummary,
                        // news_links otomatis di-cast ke JSON oleh model karena properti $casts sudah kita atur ke array
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
                        'fetched_at' => Carbon::createFromTimestamp(File::lastModified($jsonPath))
                                        ->translatedFormat('d F Y, H:i') . ' WIB'
                    ]);
                }
                $this->command->info("🚀 Seeding Sukses! 50 data tren telah masuk ke PostgreSQL.");
            } else {
                $this->command->error("File JSON ditemukan tetapi tidak berisi data valid.");
            }
        } else {
            $this->command->warn("File JSON tidak ditemukan di $jsonPath. Pastikan scraper.py berhasil dijalankan sebelum seeding.");
        }
    }
}