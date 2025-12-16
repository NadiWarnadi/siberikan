@extends('layouts.app')

@section('title', 'History Rejected - SIBERIKAN')

@section('content')
<style>
    .header-section {
        background: linear-gradient(135deg, #dc3545, #bb2d3b);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .history-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 4px solid #dc3545;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .history-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 15px;
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 1px solid #dee2e6;
    }

    .code-info {
        display: flex;
        flex-direction: column;
    }

    .code {
        font-weight: 700;
        color: #0d6efd;
        font-size: 1rem;
    }

    .nelayan-name {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .rejected-badge {
        background: #f8d7da;
        color: #721c24;
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .card-details {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 15px;
    }

    .detail-item {
        text-align: center;
    }

    .detail-label {
        font-size: 0.85rem;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
    }

    .rejection-reason {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        border-left: 3px solid #dc3545;
    }

    .rejection-reason-label {
        font-size: 0.85rem;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .rejection-reason-text {
        color: #721c24;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .rejection-info {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 15px;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 15px;
        align-items: center;
    }

    .rejection-info-icon {
        font-size: 1.5rem;
        color: #dc3545;
    }

    .rejection-info-text {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .rejection-info-text strong {
        color: #212529;
    }

    .card-actions {
        display: flex;
        gap: 10px;
    }

    .btn-small {
        padding: 8px 12px;
        font-size: 0.85rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        font-weight: 600;
        display: inline-block;
    }

    .btn-view {
        background: #0d6efd;
        color: white;
    }

    .btn-view:hover {
        background: #0056b3;
        transform: translateY(-2px);
    }

    .search-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .search-group {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .search-group input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 0.95rem;
    }

    .search-group input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        outline: none;
    }

    .btn-search {
        background: #0d6efd;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-search:hover {
        background: #0056b3;
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
    }

    .empty-icon {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .stat-box {
        background: linear-gradient(135deg, #dc3545, #bb2d3b);
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
    }

    .stat-label {
        font-size: 0.85rem;
        opacity: 0.9;
    }
</style>

<div class="container-fluid py-4">
    <div class="header-section">
        <h1><i class="bi bi-x-circle"></i> History Penawaran Ditolak</h1>
        <p>Daftar semua penawaran yang telah ditolak beserta alasan penolakan</p>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-number">{{ $penawarans->count() }}</div>
            <div class="stat-label">Total Ditolak</div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="search-group">
            <input type="text" id="searchInput" placeholder="Cari Kode Penawaran...">
            <button class="btn-search" onclick="applySearch()">
                <i class="bi bi-search"></i> Cari
            </button>
            <button class="btn-search" style="background: #6c757d;" onclick="resetSearch()">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </button>
        </div>
    </div>

    <!-- History List -->
    @if($penawarans->count() > 0)
        <div id="historyContainer">
            @foreach($penawarans as $penawaran)
                <div class="history-card" data-searchtext="{{ $penawaran->kode_penawaran }}">
                    <div class="card-header">
                        <div class="code-info">
                            <span class="code">{{ $penawaran->kode_penawaran }}</span>
                            <span class="nelayan-name">
                                <i class="bi bi-person"></i> {{ $penawaran->nelayan->nama ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="rejected-badge">
                            <i class="bi bi-x-circle"></i> DITOLAK
                        </div>
                    </div>

                    <div class="card-details">
                        <div class="detail-item">
                            <div class="detail-label"><i class="bi bi-fish"></i> Jenis Ikan</div>
                            <div class="detail-value">{{ $penawaran->jenisIkan->nama_jenis ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label"><i class="bi bi-weight"></i> Jumlah</div>
                            <div class="detail-value">{{ number_format($penawaran->jumlah_kg, 2) }} kg</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label"><i class="bi bi-tag"></i> Harga/kg</div>
                            <div class="detail-value">Rp {{ number_format($penawaran->harga_per_kg, 0, ',', '.') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label"><i class="bi bi-calendar"></i> Tanggal</div>
                            <div class="detail-value">{{ \Carbon\Carbon::parse($penawaran->created_at)->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    <div class="rejection-reason">
                        <div class="rejection-reason-label">
                            <i class="bi bi-exclamation-circle"></i> Alasan Penolakan
                        </div>
                        <div class="rejection-reason-text">
                            {{ $penawaran->alasan_reject ?? 'Tidak ada alasan yang dicatat' }}
                        </div>
                    </div>

                    <div class="rejection-info">
                        <div class="rejection-info-icon"><i class="bi bi-x-circle"></i></div>
                        <div class="rejection-info-text">
                            Ditolak oleh <strong>{{ $penawaran->approver->nama ?? 'N/A' }}</strong> 
                            pada <strong>{{ \Carbon\Carbon::parse($penawaran->approved_at)->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>

                    <div class="card-actions">
                        <a href="{{ route('tengkulak.detail-penawaran-approval', $penawaran->id) }}" class="btn-small btn-view">
                            <i class="bi bi-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-x-circle"></i></div>
            <h5>Belum Ada Penawaran yang Ditolak</h5>
            <p class="text-muted">Penawaran yang Anda tolak akan muncul di sini dengan alasan penolakan</p>
        </div>
    @endif
</div>

<script>
function applySearch() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('[data-searchtext]');

    cards.forEach(card => {
        const text = card.getAttribute('data-searchtext').toLowerCase();
        card.style.display = text.includes(search) ? '' : 'none';
    });
}

function resetSearch() {
    document.getElementById('searchInput').value = '';
    document.querySelectorAll('[data-searchtext]').forEach(card => {
        card.style.display = '';
    });
}

// Search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applySearch();
    }
});
</script>
@endsection
