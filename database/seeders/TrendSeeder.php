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
        // 1. Cek apakah tabel trends sudah ada
        if (Schema::hasTable('trends')) {
            Trend::truncate();
        } else {
            $this->command->warn("Tabel 'trends' belum ditemukan. Pastikan migrasi sudah berjalan.");
            return;
        }

        // 2. Path ke file JSON
        $jsonPath = storage_path('app/trends.json');

        // Cek path alternatif jika tidak ditemukan di storage
        if (!File::exists($jsonPath)) {
            $jsonPath = base_path('trends.json');
        }

        if (File::exists($jsonPath)) {
            $jsonContent = File::get($jsonPath);
            $trendsData = json_decode($jsonContent, true);

            if ($trendsData) {
                $this->command->info("Memproses " . count($trendsData) . " data dari JSON...");

                // Konfigurasi Keyword Mapping
                $mapping = [
                    'Teknologi'  => ['ai', 'tech', 'iphone', 'apple', 'samsung', 'gadget', 'crypto', 'bitcoin', 'software', 'chatgpt', 'deepseek', 'coding', 'digital', 'robot', 'ps5', 'windows', 'ios', 'android', 'chain'],
                    'Hiburan'    => ['taylor', 'concert', 'bts', 'movie', 'film', 'artis', 'kpop', 'nct', 'netflix', 'konser', 'musik', 'album', 'trailer', 'seleb', 'drama', 'bioskop', 'vlog', 'youtube', 'tiktok', 'standup', 'komedi'],
                    'Olahraga'   => ['bola', 'liga', 'match', 'fc', 'united', 'madrid', 'timnas', 'skor', 'badminton', 'motogp', 'pssi', 'champion', 'f1', 'atlet', 'juara', 'persib', 'persija', 'voley', 'basket'],
                    'Politik'    => ['ikn', 'presiden', 'menteri', 'dpr', 'dprd', 'pemilu', 'pilkada', 'kpk', 'hukum', 'rakyat', 'politik', 'pemerintah', 'asn', 'negara', 'demokrasi', 'uud', 'sidang', 'partai', 'prajurit', 'kabinet', 'prabowo', 'gibran'],
                    'Gaya Hidup' => ['diet', 'fashion', 'skincare', 'minimalis', 'kuliner', 'travel', 'wisata', 'kopi', 'masak', 'gaya', 'hidup', 'lifestyle', 'sehat', 'belanja', 'outfit', 'parfum'],
                ];

                foreach ($trendsData as $item) {
                    $title = $item['name'] ?? 'Tanpa Judul';
                    
                    // Ambil Ranking (PENTING untuk pengurutan di Home)
                    $rank = $item['rank'] ?? 999;

                    // Logika Kategorisasi Otomatis
                    $finalCategory = 'Umum';
                    if (isset($item['domainContext']) && $item['domainContext'] !== 'Umum') {
                        $finalCategory = $item['domainContext'];
                    } else {
                        $loweredTitle = strtolower($title);
                        foreach ($mapping as $category => $keywords) {
                            foreach ($keywords as $keyword) {
                                if (str_contains($loweredTitle, $keyword)) {
                                    $finalCategory = $category;
                                    break 2;
                                }
                            }
                        }
                    }

                    // Gunakan Summary Manual jika ada, jika tidak gunakan placeholder
                    $finalSummary = !empty($item['manual_summary']) 
                                    ? $item['manual_summary'] 
                                    : "Ringkasan belum tersedia.";

                    // Penanganan jumlah postingan kosong
                    $postCount = $item['tweet_count'] ?? '';
                    if (empty($postCount)) {
                        $postCount = 'Jumlah tidak tersedia';
                    }

                    Trend::create([
                        'rank'       => $rank, // Kolom ini wajib ada agar urutan sesuai trend24
                        'title'      => $title,
                        'category'   => $finalCategory,
                        'post_count' => $postCount,
                        'summary'    => $finalSummary,
                        'news_links' => [
                            [
                                'title' => 'Cari di Google News', 
                                'url'   => 'https://www.google.com/search?q=' . urlencode($title . ' news') . '&tbm=nws'
                            ]
                        ],
                        // Menggunakan tanggal modifikasi file JSON
                        'fetched_at' => Carbon::createFromTimestamp(File::lastModified($jsonPath))
                                        ->translatedFormat('d F Y')
                    ]);
                }

                $this->command->info("🚀 Seeding Sukses! Data telah diperbarui.");
            }
        } else {
            $this->command->warn("File trends.json tidak ditemukan. Pastikan file sudah di-upload ke Railway.");
        }
    }
}