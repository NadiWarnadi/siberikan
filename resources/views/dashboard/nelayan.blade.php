@extends('layouts.app')

@section('title', 'Dashboard Nelayan')

@section('content')
<style>
    .btn-action {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }
    
    .badge-status {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-speedboat"></i> Dashboard Nelayan</h2>
            <p class="text-muted">Kelola penawaran dan data tangkapan ikan Anda</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <a href="{{ route('nelayan.create-penawaran-form') }}" class="btn btn-primary w-100 py-3">
                                <i class="bi bi-plus-circle"></i>
                                <div class="small mt-2">Buat Penawaran Ikan</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('nelayan.list-penawarans') }}" class="btn btn-info w-100 py-3">
                                <i class="bi bi-list-check"></i>
                                <div class="small mt-2">Daftar Penawaran Saya</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('nelayan.dashboard') }}" class="btn btn-success w-100 py-3">
                                <i class="bi bi-fish"></i>
                                <div class="small mt-2">Data Tangkapan</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Penawaran Summary -->
    @if(isset($penawaranStats))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-warning mb-0">{{ $penawaranStats['pending'] ?? 0 }}</h5>
                    <small class="text-muted">Pending Approval</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-success mb-0">{{ $penawaranStats['approved'] ?? 0 }}</h5>
                    <small class="text-muted">Sudah Approved</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-danger mb-0">{{ $penawaranStats['rejected'] ?? 0 }}</h5>
                    <small class="text-muted">Ditolak</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-info mb-0">{{ $penawaranStats['total'] ?? 0 }}</h5>
                    <small class="text-muted">Total Penawaran</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Penawaran List (Recent) -->
    @if(isset($penawaranRecent) && count($penawaranRecent) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Penawaran Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Jenis Ikan</th>
                                    <th>Jumlah (kg)</th>
                                    <th>Harga/Kg</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penawaranRecent as $penawaran)
                                <tr>
                                    <td><strong>{{ $penawaran->kode_penawaran }}</strong></td>
                                    <td>{{ $penawaran->jenisIkan->nama_ikan ?? 'N/A' }}</td>
                                    <td>{{ number_format($penawaran->jumlah_kg, 2) }}</td>
                                    <td>Rp {{ number_format($penawaran->harga_per_kg, 0, ',', '.') }}</td>
                                    <td>
                                        @if($penawaran->status == 'draft')
                                            <span class="badge bg-secondary badge-status">Draft</span>
                                        @elseif($penawaran->status == 'pending')
                                            <span class="badge bg-warning badge-status">Pending</span>
                                        @elseif($penawaran->status == 'approved')
                                            <span class="badge bg-success badge-status">Approved ✓</span>
                                        @elseif($penawaran->status == 'rejected')
                                            <span class="badge bg-danger badge-status">Rejected ✗</span>
                                        @elseif($penawaran->status == 'sold_out')
                                            <span class="badge bg-info badge-status">Sold Out</span>
                                        @else
                                            <span class="badge bg-secondary badge-status">{{ $penawaran->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $penawaran->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('nelayan.detail-penawaran', $penawaran->id) }}" class="btn btn-sm btn-outline-primary btn-action">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

<!-- Add New Catch Form -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Tambah Hasil Tangkapan Baru
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('nelayan.tangkapan.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Jenis Ikan</label>
                            <select name="jenis_ikan_id" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach($jenisIkan as $ikan)
                                    <option value="{{ $ikan->id }}">{{ $ikan->nama_ikan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Berat (Kg)</label>
                            <input type="number" name="berat" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Grade</label>
                            <select name="grade" class="form-select" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Harga/Kg (Rp)</label>
                            <input type="number" name="harga_per_kg" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Tangkap</label>
                            <input type="date" name="tanggal_tangkap" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-9">
                            <label class="form-label">Catatan (Opsional)</label>
                            <input type="text" name="catatan" class="form-control" placeholder="Contoh: Tangkapan dari Laut Jawa">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Simpan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('nelayan.dashboard') }}">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari nama ikan..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="terjual" {{ request('status') == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                <option value="rusak" {{ request('status') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="grade" class="form-select">
                                <option value="">Semua Grade</option>
                                <option value="A" {{ request('grade') == 'A' ? 'selected' : '' }}>Grade A</option>
                                <option value="B" {{ request('grade') == 'B' ? 'selected' : '' }}>Grade B</option>
                                <option value="C" {{ request('grade') == 'C' ? 'selected' : '' }}>Grade C</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-table"></i> Daftar Hasil Tangkapan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Jenis Ikan</th>
                                <th>Berat (Kg)</th>
                                <th>Grade</th>
                                <th>Harga/Kg</th>
                                <th>Total Harga</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hasilTangkapan as $index => $tangkapan)
                            <tr>
                                <td>{{ $hasilTangkapan->firstItem() + $index }}</td>
                                <td><strong>{{ $tangkapan->jenisIkan->nama_ikan }}</strong></td>
                                <td>{{ number_format($tangkapan->berat, 2) }}</td>
                                <td><span class="badge bg-info">{{ $tangkapan->grade }}</span></td>
                                <td>Rp {{ number_format($tangkapan->harga_per_kg, 0, ',', '.') }}</td>
                                <td><strong>Rp {{ number_format($tangkapan->berat * $tangkapan->harga_per_kg, 0, ',', '.') }}</strong></td>
                                <td>{{ $tangkapan->tanggal_tangkap->format('d/m/Y') }}</td>
                                <td>
                                    @if($tangkapan->status == 'tersedia')
                                        <span class="badge bg-success">Tersedia</span>
                                    @elseif($tangkapan->status == 'terjual')
                                        <span class="badge bg-secondary">Terjual</span>
                                    @else
                                        <span class="badge bg-danger">Rusak</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#editModal{{ $tangkapan->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('nelayan.tangkapan.destroy', $tangkapan->id) }}" 
                                          class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $tangkapan->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Data Tangkapan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('nelayan.tangkapan.update', $tangkapan->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Jenis Ikan</label>
                                                    <select name="jenis_ikan_id" class="form-select" required>
                                                        @foreach($jenisIkan as $ikan)
                                                            <option value="{{ $ikan->id }}" 
                                                                {{ $tangkapan->jenis_ikan_id == $ikan->id ? 'selected' : '' }}>
                                                                {{ $ikan->nama_ikan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Berat (Kg)</label>
                                                    <input type="number" name="berat" class="form-control" 
                                                           value="{{ $tangkapan->berat }}" step="0.01" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Grade</label>
                                                    <select name="grade" class="form-select" required>
                                                        <option value="A" {{ $tangkapan->grade == 'A' ? 'selected' : '' }}>A</option>
                                                        <option value="B" {{ $tangkapan->grade == 'B' ? 'selected' : '' }}>B</option>
                                                        <option value="C" {{ $tangkapan->grade == 'C' ? 'selected' : '' }}>C</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Harga/Kg (Rp)</label>
                                                    <input type="number" name="harga_per_kg" class="form-control" 
                                                           value="{{ $tangkapan->harga_per_kg }}" step="0.01" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal Tangkap</label>
                                                    <input type="date" name="tanggal_tangkap" class="form-control" 
                                                           value="{{ $tangkapan->tanggal_tangkap->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="tersedia" {{ $tangkapan->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                                        <option value="terjual" {{ $tangkapan->status == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                                        <option value="rusak" {{ $tangkapan->status == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan</label>
                                                    <input type="text" name="catatan" class="form-control" 
                                                           value="{{ $tangkapan->catatan }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Belum ada data hasil tangkapan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($hasilTangkapan->hasPages())
            <div class="card-footer">
                {{ $hasilTangkapan->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
