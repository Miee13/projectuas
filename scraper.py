import requests
from bs4 import BeautifulSoup
import json
import os

def scrape_trends24():
    # URL Trends24 Indonesia
    url = "https://trends24.in/indonesia/"
    
    # Header yang lebih lengkap untuk mensimulasikan browser asli
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Accept-Language": "en-US,en;q=0.9,id;q=0.8",
        "Referer": "https://trends24.in/"
    }

    print(f"Sedang mengambil data dari {url}...")
    
    try:
        response = requests.get(url, headers=headers, timeout=15)
        response.raise_for_status()
        soup = BeautifulSoup(response.text, 'html.parser')

        # Mencoba beberapa selektor berbeda untuk menemukan kartu tren terbaru
        # Trends24 biasanya meletakkan tren terbaru di kartu pertama
        latest_card = soup.select_one('.trend-card') or \
                      soup.select_one('#trend-list .trend-card') or \
                      soup.find('div', class_='trend-card')

        if not latest_card:
            # Jika kartu tidak ditemukan, coba cari daftar (ol/ul) langsung
            latest_card = soup.find('ol') or soup.find('ul', class_='trend-list')

        if not latest_card:
            print("Gagal menemukan data tren di halaman. Struktur situs mungkin telah berubah.")
            return

        # Mengambil semua item list (li) di dalam kartu/daftar tersebut
        trend_items = latest_card.find_all('li')
        if not trend_items:
            print("Kartu ditemukan, tapi tidak ada item tren di dalamnya.")
            return

        trends_list = []

        # Pemetaan Kata Kunci (Keyword Mapping) untuk Kategorisasi
        keywords_map = {
            "Teknologi": ["ai", "iphone", "samsung", "chatgpt", "google", "crypto", "bitcoin", "tech", "gadget", "deepseek", "coding", "software", "chip", "robot"],
            "Hiburan": ["taylor", "bts", "konser", "film", "movie", "artis", "seleb", "netflix", "kpop", "drama", "album", "musik", "trailer", "streaming"],
            "Olahraga": ["bola", "liga", "match", "skor", "fc", "united", "madrid", "timnas", "badminton", "motogp", "f1", "atlet", "pssi", "juara"],
            "Politik": ["ikn", "presiden", "menteri", "pilkada", "pemilu", "dpr", "rakyat", "pemerintah", "hukum", "negara", "politik", "demokrasi", "uud"],
            "Gaya Hidup": ["diet", "fashion", "skincare", "minimalis", "masak", "travel", "wisata", "kuliner", "kopi", "sehat", "fashionable", "makan"]
        }

        def categorize(title):
            title_lower = title.lower()
            for category, keywords in keywords_map.items():
                for word in keywords:
                    if word in title_lower:
                        return category
            return "Umum"

        for index, item in enumerate(trend_items):
            # Mencari teks tren di dalam tag <a>
            name_tag = item.find('a')
            if not name_tag:
                continue
            
            name = name_tag.text.strip()
            
            # Mencari jumlah tweet (dalam span dengan class 'tweet-count')
            count_tag = item.find('span', class_='tweet-count')
            tweet_count = count_tag.text.strip() if count_tag else "N/A"

            trends_list.append({
                "rank": str(index + 1),
                "name": name,
                "tweet_count": tweet_count,
                "domainContext": categorize(name),
                "last_updated": "Baru saja"
                "manual_summary": ""
            })

        if not trends_list:
            print("Tidak ada data tren yang berhasil diproses.")
            return

        # Menentukan direktori penyimpanan otomatis (storage/app/)
        output_dir = os.path.join('storage', 'app')
        if not os.path.exists(output_dir):
            os.makedirs(output_dir, exist_ok=True)
            
        output_file = os.path.join(output_dir, "trends.json")
        
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(trends_list, f, indent=2)

        print(f"Berhasil! {len(trends_list)} tren disimpan ke {output_file}")

    except requests.exceptions.RequestException as e:
        print(f"Kesalahan koneksi: {e}")
    except Exception as e:
        print(f"Terjadi kesalahan sistem: {e}")

if __name__ == "__main__":
    scrape_trends24()