<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trend;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class TrendSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cek Tabel
        if (!Schema::hasTable('trends')) {
            $this->command->error("❌ Tabel 'trends' belum dibuat. Migrasi gagal.");
            return;
        }

        // 2. Bersihkan Data
        Trend::truncate();

        // 3. Cek Keberadaan File JSON
        $jsonPath = storage_path('app/trends.json');
        $this->command->info("🔍 Mencari file di: " . $jsonPath);

        if (!File::exists($jsonPath)) {
            $this->command->error("❌ GAGAL: File trends.json tidak ditemukan!");
            
            // DEBUG: Tampilkan isi folder agar kita tahu file nyasar ke mana
            $dir = storage_path('app');
            if (File::isDirectory($dir)) {
                $files = File::files($dir);
                $this->command->warn("📂 Isi folder storage/app saat ini:");
                foreach ($files as $file) {
                    $this->command->warn(" - " . $file->getFilename());
                }
                if (empty($files)) $this->command->warn(" - (Folder Kosong)");
            } else {
                $this->command->error("📂 Folder storage/app bahkan tidak ada!");
            }
            return;
        }

        // 4. Proses Data
        $jsonContent = File::get($jsonPath);
        $trendsData = json_decode($jsonContent, true);

        if (empty($trendsData)) {
            $this->command->error("❌ File JSON ada, tapi kosong/corrupt.");
            return;
        }

        $this->command->info("✅ File ditemukan! Memproses " . count($trendsData) . " data...");

        foreach ($trendsData as $item) {
            $title = $item['name'] ?? 'Tanpa Judul';
            $postCount = $item['tweet_count'] ?? '';
            // Jika kosong, beri nilai default agar tidak aneh di tampilan
            if (empty($postCount)) $postCount = "Trending";

            Trend::create([
                'title'      => $title,
                'category'   => $item['domainContext'] ?? 'Umum',
                'post_count' => $postCount,
                'summary'    => "Topik '$title' sedang hangat dibicarakan.",
                'news_links' => [
                    ['title' => 'Cek di X', 'url' => 'https://x.com/search?q=' . urlencode($title)]
                ],
                // Gunakan waktu sekarang jika file baru dibuat
                'fetched_at' => date('d F Y, H:i') . ' WIB'
            ]);
        }

        $this->command->info("🚀 SUKSES! Data berhasil disimpan ke PostgreSQL.");
    }
}