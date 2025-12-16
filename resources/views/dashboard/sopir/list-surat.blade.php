@extends('layouts.app')

@section('title', 'Daftar Surat Pengiriman - SIBERIKAN')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0"><i class="bi bi-file-earmark-text"></i> Surat Pengiriman Saya</h2>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sopir.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if($pengiriman->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th><i class="bi bi-file-earmark"></i> Nomor Resi</th>
                        <th><i class="bi bi-person"></i> Pembeli</th>
                        <th><i class="bi bi-geo-alt"></i> Alamat Tujuan</th>
                        <th><i class="bi bi-calendar"></i> Tanggal Kirim</th>
                        <th><i class="bi bi-hourglass"></i> Est. Tiba</th>
                        <th><i class="bi bi-info-circle"></i> Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengiriman as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->nomor_resi }}</strong>
                            </td>
                            <td>
                                {{ $item->transaksi->pembeli->nama ?? 'Unknown' }}
                            </td>
                            <td>
                                <small>{{ Str::limit($item->alamat_tujuan, 40) }}</small>
                            </td>
                            <td>
                                {{ $item->tanggal_kirim->format('d M Y') }}
                            </td>
                            <td>
                                {{ $item->tanggal_estimasi->format('d M Y') }}
                                @if(now() > $item->tanggal_estimasi && $item->status != 'terkirim')
                                    <span class="badge bg-danger ms-2">Terlambat</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($item->status == 'dikirim')
                                    <span class="badge bg-info">Dikirim</span>
                                @elseif($item->status == 'terkirim')
                                    <span class="badge bg-success">Terkirim</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('sopir.view-surat', $item->nomor_resi) }}" 
                                       class="btn btn-sm btn-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('sopir.download-surat', $item->nomor_resi) }}" 
                                       class="btn btn-sm btn-info" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $pengiriman->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Anda belum memiliki surat pengiriman. Tunggu hingga ada order baru yang diberikan kepada Anda.
        </div>
    @endif
</div>
@endsection
