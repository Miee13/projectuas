<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z - Platform Social Listening</title>
    <!-- Google Fonts: Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>
<body>

   <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('trends.index') }}">Z</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('trends.index') ? 'active-nav' : '' }}" href="{{ route('trends.index') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('trends.trending') ? 'active-nav' : '' }}" href="{{ route('trends.trending') }}">Sedang Tren</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('trends.about') ? 'active-nav' : '' }}" href="{{ route('trends.about') }}">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- MODAL DETAIL -->
    <div class="modal fade" id="trendDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-4" style="border-radius: 30px; border: none;">
                <div class="modal-header border-0">
                    <h2 class="modal-title h4 fw-bold" id="modalTitle">Judul Tren</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary px-3 py-2" id="modalPostCount">0 Postingan</span>
                            <small class="text-muted" id="modalDate">Diambil: -</small>
                        </div>
                        <p class="lead" id="modalSummary">Ringkasan akan muncul di sini...</p>
                    </div>
                    <h3 class="h6 fw-bold mb-3">Berita Terkait</h3>
                    <div id="modalNews"></div>
                </div>
                <div class="modal-footer border-0 justify-content-between">
                    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" id="btnExternalX" target="_blank" class="btn btn-dark btn-rounded d-flex align-items-center">
                        <i data-lucide="external-link" class="me-2" style="width: 18px"></i> Lanjut ke Platform X
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-5 opacity-50">
        <div class="container">
            <hr class="mb-4">
            <p>&copy; 2026 Platform Z. Minimalis, Cepat, Tanpa Akun.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();

        function showDetail(data) {
            // 1. Pastikan element ada sebelum mengisi
            const titleEl = document.getElementById('modalTitle');
            const countEl = document.getElementById('modalPostCount');
            const summaryEl = document.getElementById('modalSummary');
            const dateEl = document.getElementById('modalDate');
            const newsContainer = document.getElementById('modalNews');
            const btnX = document.getElementById('btnExternalX');

            if (!titleEl || !countEl || !summaryEl || !newsContainer || !dateEl) {
                console.error("Salah satu elemen modal tidak ditemukan di HTML!");
                return;
            }

            // 2. Isi Data
            titleEl.innerText = data.title;
            countEl.innerText = data.post_count + (data.post_count.includes('post') ? '' : ' Postingan');
            summaryEl.innerText = data.summary;
            dateEl.innerText = "Diambil: " + (data.fetched_at || "-");
            btnX.href = "https://x.com/search?q=" + encodeURIComponent(data.title);

            // 3. Render Berita
            newsContainer.innerHTML = '';
            let links = [];
            try {
                links = typeof data.news_links === 'string' ? JSON.parse(data.news_links) : data.news_links;
            } catch (e) {
                console.error("Gagal parse news_links", e);
            }

            if (links && links.length > 0) {
                links.forEach(item => {
                    newsBoxItem = `
                        <a href="${item.url}" target="_blank" class="news-link">
                            <div class="d-flex align-items-center">
                                <i data-lucide="newspaper" class="me-3 text-muted" style="width: 20px"></i> 
                                <span>${item.title}</span>
                            </div>
                        </a>
                    `;
                    newsContainer.insertAdjacentHTML('beforeend', newsBoxItem);
                });
            } else {
                newsContainer.innerHTML = '<p class="text-muted small">Tidak ada berita terkait saat ini.</p>';
            }

            // 4. Tampilkan Modal
            const myModal = new bootstrap.Modal(document.getElementById('trendDetailModal'));
            myModal.show();
            lucide.createIcons();
        }
    </script>
</body>
</html>