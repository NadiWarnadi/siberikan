<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoiceNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 40px;
            gap: 20px;
        }

        .company-info h2 {
            color: #0d6efd;
            margin-bottom: 5px;
        }

        .company-info p {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 3px;
        }

        .invoice-title {
            text-align: right;
            grid-column: 2;
            align-self: start;
        }

        .invoice-title h1 {
            font-size: 2rem;
            color: #212529;
            margin-bottom: 5px;
        }

        .invoice-number {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 30px;
            gap: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .detail-block h4 {
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .detail-block p {
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        .detail-block p strong {
            color: #212529;
        }

        .detail-block p span {
            color: #6c757d;
        }

        table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        table thead {
            background: #0d6efd;
            color: white;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary-section {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-table {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: #6c757d;
            font-weight: 600;
        }

        .summary-value {
            color: #212529;
            font-weight: 600;
        }

        .summary-total {
            background: white;
            padding: 15px;
            border-radius: 6px;
            font-size: 1.2rem;
            font-weight: 700;
            color: #0d6efd;
        }

        .payment-terms {
            grid-column: 1 / -1;
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .payment-terms h4 {
            color: #212529;
            margin-bottom: 10px;
        }

        .payment-terms p {
            color: #6c757d;
            margin-bottom: 5px;
        }

        .footer {
            border-top: 2px solid #dee2e6;
            padding-top: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 0.85rem;
        }

        .approved-stamp {
            background: #d4edda;
            border: 2px solid #198754;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        .approved-stamp strong {
            display: block;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .print-button {
            display: inline-block;
            background: #0d6efd;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .print-button:hover {
            background: #0056b3;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                padding: 0;
            }

            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <button class="print-button" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak / Download PDF
        </button>

        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h2>SIBERIKAN</h2>
                <p>Sistem Informasi Distribusi Ikan</p>
                <p>Jl. Nelayan Raya No. 123</p>
                <p>Telepon: (021) 1234-5678</p>
                <p>Email: info@siberikan.local</p>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="invoice-number">
                    <strong>{{ $invoiceNumber }}</strong><br>
                    Tanggal: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <!-- Approval Stamp -->
        <div class="approved-stamp">
            <strong>✓ PENAWARAN DISETUJUI</strong>
            Disetujui pada: {{ \Carbon\Carbon::parse($penawaran->approved_at)->format('d/m/Y H:i') }}
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="detail-block">
                <h4>Dari:</h4>
                <p><strong>{{ $penawaran->approver->nama ?? 'Tengkulak' }}</strong></p>
                <p><span>(Tengkulak)</span></p>
            </div>
            <div class="detail-block">
                <h4>Nelayan:</h4>
                <p><strong>{{ $penawaran->nelayan->nama ?? 'N/A' }}</strong></p>
                <p><span>{{ $penawaran->nelayan->no_telepon ?? 'N/A' }}</span></p>
                <p><span>{{ $penawaran->nelayan->alamat ?? 'N/A' }}</span></p>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Deskripsi Ikan</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-right">Harga per kg</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $penawaran->jenisIkan->nama_jenis ?? 'N/A' }}</strong><br>
                        <small style="color: #6c757d;">
                            Kode: {{ $penawaran->kode_penawaran }}<br>
                            Kualitas: {{ ucfirst($penawaran->kualitas ?? 'Standar') }}<br>
                            Tanggal Tangkap: {{ \Carbon\Carbon::parse($penawaran->tanggal_tangkapan)->format('d/m/Y') }}
                        </small>
                    </td>
                    <td class="text-center">{{ number_format($penawaran->jumlah_kg, 2) }} kg</td>
                    <td class="text-right">Rp {{ number_format($penawaran->harga_per_kg, 0, ',', '.') }}</td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($penawaran->jumlah_kg * $penawaran->harga_per_kg, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-section">
            <div></div>
            <div class="summary-table">
                <div class="summary-row">
                    <span class="summary-label">Subtotal:</span>
                    <span class="summary-value">Rp {{ number_format($penawaran->jumlah_kg * $penawaran->harga_per_kg, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Pajak (10%):</span>
                    <span class="summary-value">Rp {{ number_format(($penawaran->jumlah_kg * $penawaran->harga_per_kg) * 0.1, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row" style="border: none; padding: 15px; background: #0d6efd; color: white; border-radius: 6px; margin-top: 10px; font-size: 1.1rem;">
                    <span style="color: white;">TOTAL:</span>
                    <span style="color: white; font-weight: 700;">Rp {{ number_format(($penawaran->jumlah_kg * $penawaran->harga_per_kg) * 1.1, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Terms -->
        <div class="payment-terms">
            <h4>Syarat dan Ketentuan:</h4>
            <p>✓ Pembayaran dapat dilakukan melalui transfer bank atau tunai</p>
            <p>✓ Barang harus diambil dalam 3 hari sejak approval</p>
            <p>✓ Garansi kualitas ikan sesuai dengan yang tertera pada penawaran</p>
            <p>✓ Untuk informasi lebih lanjut, hubungi Tengkulak melalui kontak yang tersedia</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini adalah bukti approval penawaran dari Nelayan. Invoice ini sudah sah dan dapat digunakan sebagai acuan transaksi.</p>
            <p style="margin-top: 15px; font-size: 0.8rem;">SIBERIKAN © 2025 - Sistem Informasi Distribusi Ikan</p>
        </div>
    </div>
</body>
</html>
