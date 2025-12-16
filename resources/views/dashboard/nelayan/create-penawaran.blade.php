@extends('layouts.app')

@section('title', 'Buat Penawaran - SIBERIKAN')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="bi bi-plus-circle"></i> Buat Penawaran Ikan Baru</h2>
            <p class="text-muted">Buat penawaran ikan tangkapan Anda untuk disetujui Tengkulak</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <form action="{{ route('nelayan.create-penawaran') }}" method="POST" enctype="multipart/form-data" id="formPenawaran">
                        @csrf

                        <!-- Jenis Ikan -->
                        <div class="mb-3">
                            <label for="jenis_ikan_id" class="form-label">Jenis Ikan *</label>
                            <select name="jenis_ikan_id" id="jenis_ikan_id" class="form-select" required>
                                <option value="">-- Pilih Jenis Ikan --</option>
                                @foreach($jenisIkan as $jenis)
                                    <option value="{{ $jenis->id }}" {{ old('jenis_ikan_id') == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_ikan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_ikan_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Jumlah & Harga -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_kg" class="form-label">Jumlah (kg) *</label>
                                <input type="number" name="jumlah_kg" id="jumlah_kg" class="form-control" 
                                       step="0.1" min="0.1" value="{{ old('jumlah_kg') }}" required>
                                @error('jumlah_kg')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="harga_per_kg" class="form-label">Harga per kg (Rp) *</label>
                                <input type="number" name="harga_per_kg" id="harga_per_kg" class="form-control" 
                                       min="1000" max="999999" value="{{ old('harga_per_kg') }}" required>
                                <small class="text-muted">Range: Rp 1.000 - Rp 999.999</small>
                                @error('harga_per_kg')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Kualitas -->
                        <div class="mb-3">
                            <label for="kualitas" class="form-label">Kualitas *</label>
                            <select name="kualitas" id="kualitas" class="form-select" required>
                                <option value="">-- Pilih Kualitas --</option>
                                <option value="premium" {{ old('kualitas') == 'premium' ? 'selected' : '' }}>
                                    Premium (Terbaik)
                                </option>
                                <option value="standar" {{ old('kualitas') == 'standar' ? 'selected' : '' }}>
                                    Standar (Biasa)
                                </option>
                                <option value="ekonomis" {{ old('kualitas') == 'ekonomis' ? 'selected' : '' }}>
                                    Ekonomis (Standar Bawah)
                                </option>
                            </select>
                            @error('kualitas')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tanggal Tangkapan -->
                        <div class="mb-3">
                            <label for="tanggal_tangkapan" class="form-label">Tanggal Tangkapan *</label>
                            <input type="date" name="tanggal_tangkapan" id="tanggal_tangkapan" class="form-control" 
                                   value="{{ old('tanggal_tangkapan', today()) }}" required>
                            @error('tanggal_tangkapan')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Lokasi Tangkapan -->
                        <div class="mb-3">
                            <label for="lokasi_tangkapan" class="form-label">Lokasi Tangkapan</label>
                            <input type="text" name="lokasi_tangkapan" id="lokasi_tangkapan" class="form-control" 
                                   placeholder="Contoh: Laut Jawa, Perairan Pelabuhan..." value="{{ old('lokasi_tangkapan') }}">
                            @error('lokasi_tangkapan')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Kedalaman -->
                        <div class="mb-3">
                            <label for="kedalaman" class="form-label">Kedalaman (meter)</label>
                            <input type="text" name="kedalaman" id="kedalaman" class="form-control" 
                                   placeholder="Contoh: 50, 100..." value="{{ old('kedalaman') }}">
                            @error('kedalaman')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Catatan -->
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3" 
                                      placeholder="Contoh: Ikan segar, baru ditangkap...">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Foto Ikan -->
                        <div class="mb-4">
                            <label for="foto_ikan" class="form-label">Foto Ikan *</label>
                            <div class="input-group mb-2">
                                <input type="file" name="foto_ikan" id="foto_ikan" class="form-control" 
                                       accept="image/*" required>
                                <span class="input-group-text">
                                    <i class="bi bi-image"></i>
                                </span>
                            </div>
                            <small class="text-muted">
                                Format: JPG, PNG, GIF | Max: 5MB
                            </small>
                            @error('foto_ikan')
                                <span class="text-danger small d-block">{{ $message }}</span>
                            @enderror

                            <!-- Preview Foto -->
                            <div id="fotoPreview" class="mt-3" style="display: none;">
                                <small class="text-muted d-block mb-2">Preview:</small>
                                <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                <i class="bi bi-check-circle"></i> Buat Penawaran
                            </button>
                            <a href="{{ route('nelayan.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Penting</h6>
                </div>
                <div class="card-body">
                    <h6>📋 Sistem Penawaran:</h6>
                    <ul class="small">
                        <li>Buat penawaran dengan detail lengkap</li>
                        <li>Unggah foto ikan untuk verifikasi</li>
                        <li>Tunggu approval dari Tengkulak</li>
                        <li>Setelah disetujui → masuk ke stok</li>
                    </ul>

                    <hr>

                    <h6>💰 Tips Harga:</h6>
                    <ul class="small">
                        <li><strong>Premium:</strong> Rp 50-100k/kg</li>
                        <li><strong>Standar:</strong> Rp 30-50k/kg</li>
                        <li><strong>Ekonomis:</strong> Rp 15-30k/kg</li>
                    </ul>

                    <hr>

                    <h6>📸 Foto Ikan:</h6>
                    <p class="small">Upload foto berkualitas tinggi untuk memastikan penawaran diterima. Foto yang jelas akan meningkatkan peluang approval.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Perhatian</h6>
                </div>
                <div class="card-body small">
                    <p>Penawaran Anda akan dievaluasi oleh Tengkulak. Pastikan harga dan data akurat untuk menghindari penolakan.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview foto
    document.getElementById('foto_ikan').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('previewImg').src = event.target.result;
                document.getElementById('fotoPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Validasi form client-side (OWASP: Input Validation)
    document.getElementById('formPenawaran').addEventListener('submit', function(e) {
        const harga = parseInt(document.getElementById('harga_per_kg').value);
        const jumlah = parseFloat(document.getElementById('jumlah_kg').value);

        if (harga < 1000 || harga > 999999) {
            e.preventDefault();
            alert('Harga harus antara Rp 1.000 - Rp 999.999');
            return false;
        }

        if (jumlah < 0.1) {
            e.preventDefault();
            alert('Jumlah harus minimal 0.1 kg');
            return false;
        }
    });
</script>

<style>
    .form-label {
        font-weight: 600;
        color: #333;
    }

    .form-control, .form-select {
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
</style>
@endsection
