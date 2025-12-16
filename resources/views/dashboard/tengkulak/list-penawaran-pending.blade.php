@extends('layouts.app')

@section('title', 'Penawaran Pending - SIBERIKAN')

@section('content')
<style>
    .header-section {
        background: linear-gradient(135deg, #0d6efd, #0056b3);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .penawaran-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .penawaran-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border-left: 4px solid #ffc107;
    }

    .penawaran-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .penawaran-header {
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .penawaran-code {
        font-weight: 700;
        color: #0d6efd;
        font-size: 0.95rem;
    }

    .penawaran-nelayan {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 5px;
    }

    .penawaran-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #e9ecef;
    }

    .penawaran-body {
        padding: 15px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }

    .info-label {
        color: #6c757d;
        font-weight: 600;
    }

    .info-value {
        color: #212529;
        font-weight: 700;
    }

    .harga-warning {
        background: #fff3cd;
        border-left: 3px solid #ffc107;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .penawaran-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        border-top: 1px solid #dee2e6;
        padding-top: 15px;
    }

    .btn-small {
        flex: 1;
        padding: 8px 12px;
        font-size: 0.85rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        display: inline-block;
    }

    .btn-approve {
        background: #198754;
        color: white;
    }

    .btn-approve:hover {
        background: #145a32;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
    }

    .btn-reject {
        background: #dc3545;
        color: white;
    }

    .btn-reject:hover {
        background: #bb2d3b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .btn-view {
        background: #0d6efd;
        color: white;
    }

    .btn-view:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }

    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .filter-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
        color: #212529;
    }

    .form-control, .form-select {
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        outline: none;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-box {
        background: linear-gradient(135deg, #0d6efd, #0056b3);
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
</style>

<div class="container-fluid py-4">
    <div class="header-section">
        <h1><i class="bi bi-inbox-fill"></i> Penawaran Pending</h1>
        <p>Kelola penawaran ikan dari nelayan dan buat keputusan approval</p>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-number">{{ $penawarans->count() }}</div>
            <div class="stat-label">Penawaran Pending</div>
        </div>
        <div class="stat-box" style="background: linear-gradient(135deg, #198754, #145a32);">
            <div class="stat-number">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
            <div class="stat-label">Total Value</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-group">
            <div class="form-group">
                <label for="filterNelayan">Filter Nelayan</label>
                <select id="filterNelayan" class="form-select">
                    <option value="">Semua Nelayan</option>
                    @foreach($nelayans as $nelayan)
                        <option value="{{ $nelayan->id }}">{{ $nelayan->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="filterIkan">Filter Jenis Ikan</label>
                <select id="filterIkan" class="form-select">
                    <option value="">Semua Jenis Ikan</option>
                    @foreach($ikans as $ikan)
                        <option value="{{ $ikan->id }}">{{ $ikan->nama_jenis }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="searchCode">Cari Kode Penawaran</label>
                <input type="text" id="searchCode" class="form-control" placeholder="Cth: PENAW-2025-001">
            </div>
        </div>
        <button type="button" class="btn btn-primary" onclick="applyFilters()">
            <i class="bi bi-funnel"></i> Terapkan Filter
        </button>
        <button type="button" class="btn btn-secondary" onclick="resetFilters()">
            <i class="bi bi-arrow-clockwise"></i> Reset
        </button>
    </div>

    <!-- Penawaran Grid -->
    @if($penawarans->count() > 0)
        <div class="penawaran-grid" id="penawaranGrid">
            @foreach($penawarans as $penawaran)
                <div class="penawaran-card" data-penawaran-id="{{ $penawaran->id }}" 
                     data-nelayan-id="{{ $penawaran->nelayan_id }}" 
                     data-ikan-id="{{ $penawaran->jenis_ikan_id }}">
                    
                    <div class="penawaran-header">
                        <div class="penawaran-code">{{ $penawaran->kode_penawaran }}</div>
                        <div class="penawaran-nelayan">
                            <i class="bi bi-person-fill"></i> 
                            {{ $penawaran->nelayan->nama ?? 'N/A' }}
                        </div>
                    </div>

                    @if($penawaran->foto_ikan)
                        <img src="{{ asset('storage/' . $penawaran->foto_ikan) }}" 
                             alt="Foto Ikan" class="penawaran-image">
                    @else
                        <div class="penawaran-image" style="display: flex; align-items: center; justify-content: center; background: #e9ecef;">
                            <i class="bi bi-image" style="font-size: 3rem; color: #999;"></i>
                        </div>
                    @endif

                    <div class="penawaran-body">
                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-fish"></i> Jenis Ikan</span>
                            <span class="info-value">{{ $penawaran->jenisIkan->nama_jenis ?? 'N/A' }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-weight"></i> Jumlah</span>
                            <span class="info-value">{{ number_format($penawaran->jumlah_kg, 2) }} kg</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-tag"></i> Harga/kg</span>
                            <span class="info-value">Rp {{ number_format($penawaran->harga_per_kg, 0, ',', '.') }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-calculator"></i> Total</span>
                            <span class="info-value">Rp {{ number_format($penawaran->jumlah_kg * $penawaran->harga_per_kg, 0, ',', '.') }}</span>
                        </div>

                        @if($penawaran->harga_per_kg > 150000)
                            <div class="harga-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                <strong>Perhatian:</strong> Harga tinggi dari rata-rata pasar
                            </div>
                        @endif

                        <div class="penawaran-actions">
                            <a href="{{ route('tengkulak.detail-penawaran-approval', $penawaran->id) }}" 
                               class="btn-small btn-view">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                            <button class="btn-small btn-approve" 
                                    onclick="approvePenawaran({{ $penawaran->id }})">
                                <i class="bi bi-check-circle"></i> Setujui
                            </button>
                            <button class="btn-small btn-reject" 
                                    onclick="rejectPenawaran({{ $penawaran->id }})">
                                <i class="bi bi-x-circle"></i> Tolak
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
            <h5>Tidak Ada Penawaran Pending</h5>
            <p class="text-muted">Tunggu nelayan untuk mengirim penawaran baru</p>
        </div>
    @endif
</div>

<script>
function applyFilters() {
    const nelayan = document.getElementById('filterNelayan').value;
    const ikan = document.getElementById('filterIkan').value;
    const code = document.getElementById('searchCode').value.toLowerCase();

    document.querySelectorAll('[data-penawaran-id]').forEach(card => {
        let show = true;

        if (nelayan && card.dataset.nelayaId != nelayan) show = false;
        if (ikan && card.dataset.ikanId != ikan) show = false;
        if (code && !card.querySelector('.penawaran-code').textContent.toLowerCase().includes(code)) show = false;

        card.style.display = show ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filterNelayan').value = '';
    document.getElementById('filterIkan').value = '';
    document.getElementById('searchCode').value = '';
    document.querySelectorAll('[data-penawaran-id]').forEach(card => {
        card.style.display = '';
    });
}

function approvePenawaran(penawaranId) {
    if (!confirm('Setujui penawaran ini? Ikan akan masuk ke stok inventory.')) return;

    fetch('{{ route("tengkulak.approve-penawaran", ":id") }}'.replace(':id', penawaranId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✓ ' + data.message);
            // Redirect to invoice generation
            window.location.href = '{{ route("tengkulak.generate-invoice", ":id") }}'.replace(':id', penawaranId);
        } else {
            alert('❌ Error: ' + (data.error || 'Terjadi kesalahan'));
        }
    })
    .catch(err => alert('❌ Error: ' + err.message));
}

function rejectPenawaran(penawaranId) {
    const alasan = prompt('Masukkan alasan penolakan (min 10 karakter):');
    if (!alasan) return;

    if (alasan.length < 10) {
        alert('❌ Alasan minimal 10 karakter');
        return;
    }

    if (alasan.length > 500) {
        alert('❌ Alasan maksimal 500 karakter');
        return;
    }

    fetch('{{ route("tengkulak.reject-penawaran", ":id") }}'.replace(':id', penawaranId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ alasan_reject: alasan })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✓ Penawaran ditolak');
            location.reload();
        } else {
            alert('❌ Error: ' + (data.error || 'Terjadi kesalahan'));
        }
    })
    .catch(err => alert('❌ Error: ' + err.message));
}
</script>
@endsection
