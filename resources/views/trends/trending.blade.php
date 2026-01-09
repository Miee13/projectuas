@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Judul Halaman -->
            <div class="d-flex align-items-center mb-4">
                <i data-lucide="trending-up" class="me-3 text-primary" style="width: 28px; height: 28px;"></i>
                <h2 class="h3 mb-0 fw-bold">Tren Untuk Anda</h2>
            </div>

            <!-- Daftar Tren Vertikal -->
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div id="verticalTrendsList">
                    @forelse($trends as $index => $trend)
                        <div class="vertical-trend-item d-flex align-items-center" onclick='showDetail(@json($trend))'>
                            <div class="fw-bold text-muted me-4" style="width: 40px; font-size: 1.2rem; text-align: center;">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-muted mb-1">{{ $trend->category }} · Sedang Tren</div>
                                <div class="fw-bold mb-1">{{ $trend->title }}</div>
                                <div class="small text-muted">{{ $trend->post_count }} postingan</div>
                            </div>
                            <i data-lucide="chevron-right" class="text-muted opacity-25 me-2"></i>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i data-lucide="info" class="mb-3 opacity-25" style="width: 48px; height: 48px;"></i>
                            <p>Belum ada data tren yang tersedia saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <p class="text-center text-muted mt-4 small">Menampilkan hingga 50 tren teratas.</p>
        </div>
    </div>
</div>
@endsection