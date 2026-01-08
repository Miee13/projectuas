<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trend;
use Illuminate\Support\Facades\File;

class TrendSeeder extends Seeder
{
    public function run(): void
    {
        Trend::truncate();

        // Cari file yang di-upload via Git
        $jsonPath = storage_path('app/trends.json');

        if (!File::exists($jsonPath)) {
            // Coba cari di root folder juga
            $jsonPath = base_path('trends.json');
        }

        if (!File::exists($jsonPath)) {
            $this->command->error("❌ Gagal: File trends.json tidak ditemukan. Pastikan Anda sudah melakukan 'git add -f' file tersebut!");
            return;
        }

        $jsonContent = File::get($jsonPath);
        $trendsData = json_decode($jsonContent, true);
    
        if (empty($trendsData)) {
            $this->command->error("❌ Gagal: File JSON kosong.");
            return;
        }

        $this->command->info("✅ File ditemukan. Memproses " . count($trendsData) . " data...");

        foreach ($trendsData as $item) {
            $title = $item['name'] ?? 'Trend';
            $tweetCount = !empty($item['tweet_count']) ? $item['tweet_count'] : "N/A";
            $category = $item['domainContext'] ?? 'Umum';

            Trend::create([
                'title'      => $title,
                'category'   => $category,
                'post_count' => $tweetCount,
                'summary'    => "Topik '$title' sedang populer di kategori $category.",
                'news_links' => [
                    ['title' => 'Cek di X', 'url' => 'https://x.com/search?q=' . urlencode($title)]
                ],
                'fetched_at' => date('d F Y, H:i') . ' WIB'
            ]);
        }

        $this->command->info("🚀 Seeding Selesai!");
    }
}