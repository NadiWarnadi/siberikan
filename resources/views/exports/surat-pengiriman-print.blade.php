<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengiriman - {{ $pengiriman->nomor_resi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #0d6efd;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 13px;
        }

        .info-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: #f9f9f9;
        }

        .info-box h3 {
            color: #0d6efd;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #0d6efd;
            padding-bottom: 5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            border: 1px solid #ddd;
            padding: 15px;
            background: white;
        }

        .info-item h4 {
            color: #0d6efd;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .info-item p {
            margin-bottom: 8px;
            font-size: 13px;
        }

        .info-item strong {
            display: block;
            color: #0d6efd;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #0d6efd;
            color: white;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: bold;
        }

        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .total-row {
            background: #0d6efd !important;
            color: white !important;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #ffc107;
            color: black;
        }

        .status-dikirim {
            background: #17a2b8;
            color: white;
        }

        .status-terkirim {
            background: #28a745;
            color: white;
        }

        .footer {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            text-align: center;
        }

        .signature {
            border-top: 1px solid #333;
            padding-top: 40px;
            font-size: 12px;
        }

        .notes {
            background: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 12px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 0;
            }
        }

        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-button button {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button button:hover {
            background: #0056b3;
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="print-button">
            <button onclick="window.print()">
                📄 Cetak / Download PDF
            </button>
        </div>

        <!-- Header -->
        <div class="header">
            <h1>🐟 SIBERIKAN</h1>
            <p>Sistem Informasi Distribusi Perikanan</p>
            <p style="margin-top: 10px; font-size: 16px; font-weight: bold; color: #0d6efd;">
                SURAT PENGIRIMAN BARANG
            </p>
        </div>

        <!-- Nomor Resi & Status -->
        <div class="info-box">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div>
                    <strong style="color: #666; font-size: 11px; text-transform: uppercase;">Nomor Resi:</strong>
                    <p style="font-size: 16px; font-weight: bold; color: #0d6efd; font-family: monospace;">
                        {{ $pengiriman->nomor_resi }}
                    </p>
                </div>
                <div>
                    <strong style="color: #666; font-size: 11px; text-transform: uppercase;">Kode Transaksi:</strong>
                    <p style="font-size: 16px; font-weight: bold; color: #0d6efd; font-family: monospace;">
                        {{ $pengiriman->transaksi->kode_transaksi }}
                    </p>
                </div>
                <div>
                    <strong style="color: #666; font-size: 11px; text-transform: uppercase;">Status:</strong>
                    <p style="margin-top: 5px;">
                        <span class="status-badge 
                            @if($pengiriman->status == 'pending') status-pending 
                            @elseif($pengiriman->status == 'dikirim') status-dikirim 
                            @else status-terkirim @endif">
                            {{ ucfirst($pengiriman->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Data Pengiriman -->
        <div class="info-grid">
            <!-- Pengirim -->
            <div class="info-item">
                <h4>📦 Pengirim (Nelayan)</h4>
                <p>
                    <strong>{{ $pengiriman->transaksi->nelayan->nama ?? 'Unknown' }}</strong>
                </p>
                <p>Email: {{ $pengiriman->transaksi->nelayan->email ?? 'N/A' }}</p>
                <p>Alamat: {{ $pengiriman->transaksi->nelayan->alamat ?? 'N/A' }}</p>
            </div>

            <!-- Penerima -->
            <div class="info-item">
                <h4>📬 Penerima (Pembeli)</h4>
                <p>
                    <strong>{{ $pengiriman->transaksi->pembeli->nama ?? 'Unknown' }}</strong>
                </p>
                <p>Email: {{ $pengiriman->transaksi->pembeli->email ?? 'N/A' }}</p>
                <p>Alamat: {{ $pengiriman->alamat_tujuan }}</p>
            </div>
        </div>

        <!-- Sopir & Tanggal -->
        <div class="info-grid">
            <!-- Sopir -->
            <div class="info-item">
                <h4>🚗 Sopir/Kurir</h4>
                <p>
                    <strong>{{ $pengiriman->sopir->nama ?? 'Unknown' }}</strong>
                </p>
                <p>Email: {{ $pengiriman->sopir->email ?? 'N/A' }}</p>
                <p>Peran: {{ ucfirst($pengiriman->sopir->peran) }}</p>
            </div>

            <!-- Tanggal & Estimasi -->
            <div class="info-item">
                <h4>📅 Jadwal Pengiriman</h4>
                <p>Tanggal Kirim: <strong>{{ $pengiriman->tanggal_kirim->format('d M Y') }}</strong></p>
                <p>Est. Tiba: <strong>{{ $pengiriman->tanggal_estimasi->format('d M Y') }}</strong></p>
                @if($pengiriman->tanggal_diterima)
                    <p>Tgl. Diterima: <strong>{{ $pengiriman->tanggal_diterima->format('d M Y') }}</strong></p>
                @endif
            </div>
        </div>

        <!-- Catatan Pengiriman -->
        @if($pengiriman->catatan)
            <div class="notes">
                <strong>📝 Catatan Pengiriman:</strong><br>
                {{ $pengiriman->catatan }}
            </div>
        @endif

        <!-- Detail Barang -->
        <h3 style="color: #0d6efd; margin-bottom: 15px; text-transform: uppercase; border-bottom: 2px solid #0d6efd; padding-bottom: 10px;">
            📦 Detail Barang Yang Dikirim
        </h3>

        <table>
            <thead>
                <tr>
                    <th>Jenis Ikan</th>
                    <th>Tanggal Tangkapan</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $totalKg = 0; @endphp
                @foreach($pengiriman->transaksi->details as $detail)
                    @php $totalKg += $detail->jumlah_kg; @endphp
                    <tr>
                        <td><strong>{{ $detail->hasilTangkapan->jenisIkan->nama_ikan ?? 'N/A' }}</strong></td>
                        <td>{{ $detail->hasilTangkapan->tanggal_tangkapan->format('d M Y') }}</td>
                        <td class="text-center">{{ number_format($detail->jumlah_kg, 2) }} kg</td>
                        <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right"><strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong></td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2"><strong>TOTAL PENGIRIMAN</strong></td>
                    <td class="text-center"><strong>{{ number_format($totalKg, 2) }} kg</strong></td>
                    <td colspan="1"></td>
                    <td class="text-right"><strong>Rp {{ number_format($pengiriman->transaksi->total_harga, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Catatan Penting -->
        <div class="notes" style="margin-top: 30px;">
            <strong>⚠️ Catatan Penting:</strong><br>
            1. Sopir wajib membawa surat pengiriman ini saat melakukan pengiriman<br>
            2. Pembeli harus menandatangani sebagai bukti penerimaan barang<br>
            3. Simpan surat ini untuk keperluan garansi dan klaim barang<br>
            4. Barang yang sudah diterima menjadi tanggung jawab pembeli
        </div>

        <!-- Footer dengan Tanda Tangan -->
        <div class="footer" style="margin-top: 50px;">
            <div class="signature">
                <p>Dibuat oleh:<br>SIBERIKAN Admin</p>
                <p style="margin-top: 30px;">........................</p>
            </div>
            <div class="signature">
                <p>Sopir/Kurir:<br>{{ $pengiriman->sopir->nama ?? 'Unknown' }}</p>
                <p style="margin-top: 30px;">........................</p>
            </div>
            <div class="signature">
                <p>Penerima:<br>{{ $pengiriman->transaksi->pembeli->nama ?? 'Unknown' }}</p>
                <p style="margin-top: 30px;">........................</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 50px; color: #999; font-size: 11px;">
            <p>Dokumen ini digenerate otomatis oleh SIBERIKAN</p>
            <p>Cetak tanggal: {{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
