// Inisialisasi ikon Lucide saat halaman dimuat
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

/**
 * Fungsi untuk menampilkan detail tren dalam modal
 * Dipanggil melalui atribut onclick di elemen HTML
 * @param {Object} data - Objek tren dari database
 */
function showDetail(data) {
    const titleEl = document.getElementById('modalTitle');
    const countEl = document.getElementById('modalPostCount');
    const summaryEl = document.getElementById('modalSummary');
    const dateEl = document.getElementById('modalDate');
    const newsContainer = document.getElementById('modalNews');
    const btnX = document.getElementById('btnExternalX');

    // Validasi keberadaan elemen agar tidak error
    if (!titleEl || !countEl || !summaryEl || !newsContainer || !dateEl) {
        console.error("Elemen modal tidak ditemukan!");
        return;
    }

    // Isi Data ke dalam Modal
    titleEl.innerText = data.title;
    // Format jumlah postingan (tambahkan teks 'Postingan' jika belum ada)
    const postCountText = String(data.post_count);
    countEl.innerText = postCountText + (postCountText.toLowerCase().includes('post') ? '' : ' Postingan');
    
    summaryEl.innerText = data.summary;
    dateEl.innerText = "Diambil: " + (data.fetched_at || "-");
    btnX.href = "https://x.com/search?q=" + encodeURIComponent(data.title);

    // Render Link Berita
    newsContainer.innerHTML = '';
    let links = [];
    try {
        // Cek tipe data news_links (string JSON atau sudah object)
        links = typeof data.news_links === 'string' ? JSON.parse(data.news_links) : data.news_links;
    } catch (e) { 
        console.error("Gagal memproses link berita:", e); 
    }

    if (Array.isArray(links) && links.length > 0) {
        links.forEach(item => {
            // Template literal untuk link berita
            const linkHtml = `
                <a href="${item.url}" target="_blank" class="news-link d-flex align-items-center">
                    <i data-lucide="newspaper" class="me-3 text-muted" style="width: 20px"></i> 
                    <span>${item.title}</span>
                </a>`;
            newsContainer.insertAdjacentHTML('beforeend', linkHtml);
        });
    } else {
        newsContainer.innerHTML = '<p class="text-muted small">Tidak ada berita terkait saat ini.</p>';
    }

    // Tampilkan Modal menggunakan Bootstrap API
    const myModal = new bootstrap.Modal(document.getElementById('trendDetailModal'));
    myModal.show();

    // Render ulang ikon Lucide (penting karena konten modal baru saja diubah)
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}