@extends('layouts.app')

@section('title', 'Detail Penawaran - SIBERIKAN')

@section('content')
<style>
    .header-section {
        background: linear-gradient(135deg, #0d6efd, #0056b3);
        color: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .content-wrapper {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .detail-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 2px solid #0d6efd;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .info-item {
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #0d6efd;
    }

    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 1rem;
        color: #212529;
        font-weight: 700;
    }

    .photo-section {
        margin-bottom: 20px;
    }

    .photo-container {
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-image {
        text-align: center;
        color: #999;
    }

    .no-image i {
        font-size: 4rem;
        margin-bottom: 10px;
        opacity: 0.5;
    }

    .approval-section {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 12px;
        padding: 20px;
        border: 2px solid #dee2e6;
    }

    .approval-form {
        margin-top: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #212529;
    }

    textarea, input {
        width: 100%;
        padding: 12px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-family: inherit;
        font-size: 0.95rem;
    }

    textarea:focus, input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        outline: none;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        flex: 1;
        text-align: center;
        text-decoration: none;
    }

    .btn-approve {
        background: #198754;
        color: white;
    }

    .btn-approve:hover {
        background: #145a32;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }

    .btn-reject {
        background: #dc3545;
        color: white;
    }

    .btn-reject:hover {
        background: #bb2d3b;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .btn-back {
        background: #6c757d;
        color: white;
    }

    .btn-back:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    }

    .warning-box {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .warning-box i {
        color: #856404;
        margin-right: 8px;
    }

    .warning-text {
        color: #856404;
        font-size: 0.95rem;
    }

    .highlight {
        background: #fff3cd;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .nelayan-info {
        background: white;
        border-radius: 8px;
        padding: 15px;
        border-left: 4px solid #0dcaf0;
        margin-bottom: 15px;
    }

    .nelayan-name {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 8px;
    }

    .nelayan-detail {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 5px;
    }
</style>

<div class="container-fluid py-4">
    <div class="header-section">
        <h1><i class="bi bi-eye"></i> Detail Penawaran</h1>
        <p>Kode: <span class="highlight">{{ $penawaran->kode_penawaran }}</span></p>
    </div>

    <div class="content-wrapper">
        <!-- Main Content -->
        <div>
            <!-- Photo Section -->
            <div class="detail-card">
                <div class="section-title">Foto Ikan</div>
                <div class="photo-section">
                    <div class="photo-container">
                        @if($penawaran->foto_ikan)
                            <img src="{{ asset('storage/' . $penawaran->foto_ikan) }}" 
                                 alt="Foto Ikan" class="photo-image">
                        @else
                            <div class="no-image">
                                <i class="bi bi-image"></i>
                                <p>Tidak ada foto</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Penawaran Details -->
            <div class="detail-card">
                <div class="section-title">Informasi Penawaran</div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><i class="bi bi-fish"></i> Jenis Ikan</div>
                        <div class="info-value">{{ $penawaran->jenisIkan->nama_jenis ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="bi bi-weight"></i> Jumlah</div>
                        <div class="info-value">{{ number_format($penawaran->jumlah_kg, 2) }} kg</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="bi bi-tag"></i> Harga per kg</div>
                        <div class="info-value">Rp {{ number_format($penawaran->harga_per_kg, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="bi bi-calculator"></i> Total Harga</div>
                        <div class="info-value">Rp {{ number_format($penawaran->jumlah_kg * $penawaran->harga_per_kg, 0, ',', '.') }}</div>
                    </div>
                </div>

                @if($penawaran->harga_per_kg > 150000)
                    <div class="warning-box">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span class="warning-text">
                            <strong>Perhatian:</strong> Harga ini lebih tinggi dari rata-rata pasar 
                            (standar: Rp 100.000 - 150.000/kg)
                        </span>
                    </div>
                @endif

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><i class="bi bi-award"></i> Kualitas</div>
                        <div class="info-value">{{ ucfirst($penawaran->kualitas ?? 'Belum ditentukan') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="bi bi-calendar"></i> Tanggal Tangkap</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($penawaran->tanggal_tangkapan)->format('d/m/Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="bi bi-geo"></i> Lokasi</div>
                        <div class="info-value">{{ $penawaran->lokasi_tangkapan ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="bi bi-water"></i> Kedalaman</div>
                        <div class="info-value">{{ $penawaran->kedalaman ?? 'N/A' }} m</div>
                    </div>
                </div>

                @if($penawaran->catatan)
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong> {{ $penawaran->catatan }}
                    </div>
                @endif
            </div>

            <!-- Nelayan Info -->
            <div class="detail-card">
                <div class="section-title">Informasi Nelayan</div>
                <div class="nelayan-info">
                    <div class="nelayan-name">{{ $penawaran->nelayan->nama ?? 'N/A' }}</div>
                    <div class="nelayan-detail">
                        <i class="bi bi-telephone"></i> {{ $penawaran->nelayan->no_telepon ?? 'N/A' }}
                    </div>
                    <div class="nelayan-detail">
                        <i class="bi bi-geo-alt"></i> {{ $penawaran->nelayan->alamat ?? 'N/A' }}
                    </div>
                    @if($penawaran->nelayan->email)
                        <div class="nelayan-detail">
                            <i class="bi bi-envelope"></i> {{ $penawaran->nelayan->email }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar - Approval Section -->
        <div>
            <div class="detail-card approval-section">
                <h3 style="margin-bottom: 15px; color: #212529;">
                    <i class="bi bi-clipboard-check"></i> Keputusan Approval
                </h3>

                <div class="nelayan-info">
                    <strong>Status Saat Ini:</strong>
                    <div style="margin-top: 8px; padding: 8px; background: #fff3cd; border-radius: 4px; text-align: center; color: #856404; font-weight: 600;">
                        <i class="bi bi-hourglass-split"></i> Menunggu Persetujuan
                    </div>
                </div>

                <!-- Approve Section -->
                <div style="margin-bottom: 20px;">
                    <h5 style="color: #198754; margin-bottom: 12px;">
                        <i class="bi bi-check-circle"></i> Setujui Penawaran
                    </h5>
                    <p style="font-size: 0.9rem; color: #6c757d; margin-bottom: 15px;">
                        Menyetujui penawaran ini akan menambahkan ikan ke inventory stok Anda. Sistem akan otomatis generate invoice.
                    </p>
                    <button type="button" class="btn btn-approve" onclick="approvePenawaran({{ $penawaran->id }})">
                        <i class="bi bi-check-circle"></i> SETUJUI PENAWARAN
                    </button>
                </div>

                <div style="border-top: 2px solid #dee2e6; padding-top: 20px;">
                    <!-- Reject Section -->
                    <h5 style="color: #dc3545; margin-bottom: 12px;">
                        <i class="bi bi-x-circle"></i> Tolak Penawaran
                    </h5>
                    <div class="approval-form">
                        <div class="form-group">
                            <label for="alasanReject">Alasan Penolakan (min 10 karakter)</label>
                            <textarea id="alasanReject" rows="5" placeholder="Jelaskan alasan penolakan..."></textarea>
                            <small style="color: #6c757d; display: block; margin-top: 5px;">
                                Min: 10 karakter | Max: 500 karakter
                            </small>
                        </div>
                        <button type="button" class="btn btn-reject" onclick="rejectPenawaran({{ $penawaran->id }})">
                            <i class="bi bi-x-circle"></i> TOLAK PENAWARAN
                        </button>
                    </div>
                </div>

                <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #dee2e6;">
                    <a href="{{ route('tengkulak.list-penawaran-pending') }}" class="btn btn-back" style="display: block; text-align: center;">
                        <i class="bi bi-arrow-left"></i> Kembali ke List
                    </a>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="detail-card" style="margin-top: 20px;">
                <div class="section-title">Ringkasan</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Jumlah Ikan</div>
                        <div class="info-value">{{ number_format($penawaran->jumlah_kg, 2) }} kg</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Harga Unit</div>
                        <div class="info-value">Rp {{ number_format($penawaran->harga_per_kg, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div style="background: #e9ecef; padding: 15px; border-radius: 6px; text-align: center;">
                    <small style="color: #6c757d; display: block; margin-bottom: 8px;">TOTAL NILAI TRANSAKSI</small>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #0d6efd;">
                        Rp {{ number_format($penawaran->jumlah_kg * $penawaran->harga_per_kg, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function approvePenawaran(penawaranId) {
    if (!confirm('Setujui penawaran ini? Ikan akan masuk ke stok inventory.\n\nAkan di-redirect ke halaman invoice generation.')) {
        return;
    }

    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'Memproses...';

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
            alert('✓ Penawaran berhasil disetujui!');
            window.location.href = data.invoice_url;
        } else {
            alert('❌ Error: ' + (data.error || 'Terjadi kesalahan'));
            btn.disabled = false;
            btn.textContent = 'SETUJUI PENAWARAN';
        }
    })
    .catch(err => {
        alert('❌ Error: ' + err.message);
        btn.disabled = false;
        btn.textContent = 'SETUJUI PENAWARAN';
    });
}

function rejectPenawaran(penawaranId) {
    const alasan = document.getElementById('alasanReject').value.trim();

    if (!alasan) {
        alert('❌ Alasan penolakan tidak boleh kosong');
        return;
    }

    if (alasan.length < 10) {
        alert('❌ Alasan minimal 10 karakter');
        return;
    }

    if (alasan.length > 500) {
        alert('❌ Alasan maksimal 500 karakter');
        return;
    }

    if (!confirm('Tolak penawaran ini? Nelayan akan menerima notifikasi.')) {
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
            alert('✓ Penawaran berhasil ditolak');
            window.location.href = '{{ route("tengkulak.list-penawaran-pending") }}';
        } else {
            alert('❌ Error: ' + (data.error || 'Terjadi kesalahan'));
        }
    })
    .catch(err => alert('❌ Error: ' + err.message));
}
</script>
@endsection
