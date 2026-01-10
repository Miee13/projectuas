@extends('layouts.app')

@section('content')
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-5 mb-4 fw-bold">Apa yang sedang hangat hari ini?</h1>
        <form action="{{ route('trends.index') }}" method="GET" class="mx-auto" style="max-width: 600px;">
            <div class="input-group mb-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-pill border-0 shadow-sm p-3 px-4" placeholder="Cari topik atau deskripsi...">
            </div>
        </form>
    </div>
</section>

<main class="container my-5">
    <!-- Filter Kategori -->
    <div class="d-flex justify-content-center flex-wrap mb-5 nav-pills">
        @foreach(['all', 'Teknologi', 'Hiburan', 'Olahraga', 'Politik', 'Gaya Hidup'] as $cat)
            <a href="{{ route('trends.index', ['category' => $cat]) }}" 
               class="nav-link-filter {{ request('category', 'all') == $cat ? 'active' : '' }}">
               {{ ucfirst($cat == 'all' ? 'Semua' : $cat) }}
            </a>
        @endforeach
    </div>

    <!-- Grid Tren -->
    <div class="row g-4">
        @forelse($trends as $trend)
            <div class="col-md-6 col-lg-4">
                <div class="card trend-card p-4" onclick='showDetail(@json($trend))'>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $trend->category }}</span>
                        <span class="small text-muted">{{ $trend->post_count }}</span>
                    </div>
                    <h3 class="h5 mt-2 fw-bold">{{ $trend->title }}</h3>
                    <p class="text-muted small mb-0">{{ Str::limit($trend->summary, 85) }}</p>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i data-lucide="search-x" class="mb-3 opacity-25" style="width: 48px; height: 48px;"></i>
                    <p>Tidak ada tren yang ditemukan untuk kata kunci tersebut.</p>
                </div>
            </div>
        @endforelse
    </div>
</main>
@endsection