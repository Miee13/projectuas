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
        // 1. Bersihkan data lama
        Trend::truncate();

        // 2. Lokasi file JSON
        $jsonPath = storage_path('app/trends.json');

        if (!File::exists($jsonPath)) {
            $jsonPath = base_path('trends.json');
        }

        if (!File::exists($jsonPath)) {
            $this->command->error("Gagal: File trends.json tidak ditemukan.");
            return;
        }

        // 3. Baca Data
        $jsonContent = File::get($jsonPath);
        $trendsData = json_decode($jsonContent, true);

        if (empty($trendsData)) {
            $this->command->error("Gagal: Isi file JSON kosong!");
            return;
        }

        $this->command->info("Memproses " . count($trendsData) . " data...");

        // 4. Proses Input Data
        foreach ($trendsData as $item) {
            $title = $item['name'] ?? 'Tanpa Judul';
            $tweetCount = !empty($item['tweet_count']) ? $item['tweet_count'] : (rand(10, 99) . "rb");
            
            // LOGIKA KATEGORI
            $finalCategory = 'Umum';
            
            // Cek JSON punya kategori bawaan
            if (isset($item['domainContext']) && $item['domainContext'] !== 'Umum') {
                $finalCategory = $item['domainContext'];
            } else {
                // cari berdasarkan Keyword Mapping yang diperluas
                $loweredTitle = strtolower($title);
                $mapping = [
                    'Teknologi'  => [
                        'ai', 'tech', 'iphone', 'apple', 'samsung', 'gadget', 'crypto', 'bitcoin', 
                        'software', 'chatgpt', 'deepseek', 'coding', 'digital', 'robot', 'ps5', 
                        'windows', 'ios', 'android', 'aplikasi', 'startup', 'fintech', 'internet',
                        'cyber', 'data', 'server', 'bug', 'glitch', 'update', 'rilis', 'fitur',
                        'hack', 'scam', 'device', 'laptop', 'pc', 'komputer', 'telkomsel', 'indosat',
                        'xl', 'jaringan', 'sinyal', 'wifi', 'machine learning', 'blockchain', 'lg', 'oppo',
                        'xiaomi', 'vivo', 'poco'
                    ],
                    'Hiburan'    => [
                        'taylor', 'concert', 'bts', 'movie', 'film', 'artis', 'kpop', 'nct', 
                        'netflix', 'konser', 'musik', 'album', 'trailer', 'seleb', 'drama', 
                        'bioskop', 'vlog', 'youtube', 'tiktok', 'idol', 'selebgram', 'viral',
                        'trending', 'video', 'stream', 'lagu', 'penyanyi', 'aktor', 'aktris',
                        'gosip', 'awards', 'piala', 'festival', 'show', 'tv', 'series', 
                        'sinetron', 'drakor', 'anime', 'manga', 'standup', 'komedi', 'lucu', 'taeyong', 'x-men',
                        'happy', 'jimin', 'actor', 'family', 'hbd', 'dj', 'mv'
                    ],
                    'Olahraga'   => [
                        'bola', 'liga', 'match', 'fc', 'united', 'madrid', 'timnas', 'skor', 
                        'badminton', 'motogp', 'pssi', 'champion', 'f1', 'atlet', 'juara', 
                        'persib', 'persija', 'voley', 'basket', 'gol', 'sport', 'olimpiade',
                        'sea games', 'asian games', 'turnamen', 'kompetisi', 'klasemen',
                        'pelatih', 'coach', 'stadion', 'supporter', 'bulutangkis', 'lakers', 'nba', 'amorim', 
                        'madrid', 'barcelona', 'arsenal', 'mancherter united', 'arne', 'martinelli'
                    ],
                    'Politik'    => [
                        'ikn', 'presiden', 'menteri', 'dpr', 'dprd', 'pemilu', 'pilkada', 'kpk', 
                        'hukum', 'rakyat', 'politik', 'pemerintah', 'asn', 'negara', 'demokrasi', 
                        'uud', 'sidang', 'partai', 'prajurit', 'kampanye', 'debat', 'caleg', 
                        'capres', 'cawapres', 'koalisi', 'oposisi', 'kebijakan', 'uu', 'bawaslu',
                        'kpu', 'korupsi', 'hakim', 'mahkamah', 'konstitusi', 'bupati', 'gubernur',
                        'walikota', 'diplomasi', 'pbb', 'demo', 'unjuk rasa', 'ormas', 'sawit', 'prabowo',
                        'gibran', 'jokowi', 'nadiem', 'trump', 'palestine', 'israel', 'perang', 'venezuela',
                        'amerika', 'buzzer', 'bapak', 'merah putih', 'pembinaan', 'pertahanan', 'sdm', 'indonesia',
                        'negeri', 'tni', 'pendidikan', 'dinasti', 'bully'
                    ],
                    'Gaya Hidup' => [
                        'diet', 'fashion', 'skincare', 'minimalis', 'kuliner', 'travel', 'wisata', 
                        'kopi', 'masak', 'gaya', 'hidup', 'lifestyle', 'sehat', 'belanja', 'outfit', 
                        'parfum', 'diskon', 'promo', 'liburan', 'hotel', 'resep', 'makanan', 
                        'minuman', 'restoran', 'cafe', 'healing', 'mental health', 'buku', 'novel',
                        'unik', 'cantik', 'ganteng', 'ootd', 'aesthetic', 'life', 'land', 'trade', 'chanel',
                        'kebaikan', 'pagi', 'honeymoon', 
                    ],
                ];

                foreach ($mapping as $category => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (str_contains($loweredTitle, $keyword)) {
                            $finalCategory = $category;
                            break 2;
                        }
                    }
                }
            }

            
            $finalSummary = !empty($item['manual_summary']) 
                            ? $item['manual_summary'] 
                            : "Ringkasan belum tersedia.";

            Trend::create([
                'title'      => $title,
                'category'   => $finalCategory,
                'post_count' => $tweetCount,
                'summary'    => $finalSummary,
                'news_links' => [
                    [
                        'title' => 'Cari di Google News', 
                        'url'   => 'https://www.google.com/search?q=' . urlencode($title) . '&tbm=nws'
                    ]
                ],
                'fetched_at' => Carbon::createFromTimestamp(File::lastModified($jsonPath))
                                ->translatedFormat('d F Y')
            ]);
        }

        $this->command->info("🚀 Seeding Sukses! Data telah diperbarui.");
    }
}