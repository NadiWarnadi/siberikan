<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\HasilTangkapan;
use App\Models\Pengguna;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pembeli = Pengguna::where('peran', 'pembeli')->get();
        $tengkulak = Pengguna::where('peran', 'tengkulak')->get();
        $hasilTangkapan = HasilTangkapan::all();

        if ($pembeli->isEmpty() || $tengkulak->isEmpty() || $hasilTangkapan->isEmpty()) {
            return;
        }

        // Transaction 1 - Pending
        $transaksi1 = Transaksi::create([
            'kode_transaksi' => 'TRX-' . date('YmdHis') . '-001',
            'tengkulak_id' => $tengkulak->first()->id,
            'pembeli_id' => $pembeli->first()->id,
            'tanggal_transaksi' => now()->subDays(5)->toDateString(),
            'total_harga' => 0,
            'status' => 'pending',
            'catatan' => 'Segera saja dikirim',
        ]);

        // Add items to transaction 1
        if ($hasilTangkapan->count() >= 1) {
            $hasil1 = $hasilTangkapan->first();
            DetailTransaksi::create([
                'transaksi_id' => $transaksi1->id,
                'hasil_tangkapan_id' => $hasil1->id,
                'jumlah_kg' => 30,
                'harga_satuan' => $hasil1->harga_per_kg,
                'subtotal' => $hasil1->harga_per_kg * 30,
            ]);
            $transaksi1->update(['total_harga' => $hasil1->harga_per_kg * 30]);
        }

        // Transaction 2 - Dikemas
        $transaksi2 = Transaksi::create([
            'kode_transaksi' => 'TRX-' . date('YmdHis') . '-002',
            'tengkulak_id' => $tengkulak->count() >= 2 ? $tengkulak->get(1)->id : $tengkulak->first()->id,
            'pembeli_id' => $pembeli->count() >= 2 ? $pembeli->get(1)->id : $pembeli->first()->id,
            'tanggal_transaksi' => now()->subDays(4)->toDateString(),
            'total_harga' => 0,
            'status' => 'dikemas',
            'catatan' => 'Pengiriman besok pagi',
        ]);

        if ($hasilTangkapan->count() >= 2) {
            $hasil2 = $hasilTangkapan->get(1);
            DetailTransaksi::create([
                'transaksi_id' => $transaksi2->id,
                'hasil_tangkapan_id' => $hasil2->id,
                'jumlah_kg' => 25,
                'harga_satuan' => $hasil2->harga_per_kg,
                'subtotal' => $hasil2->harga_per_kg * 25,
            ]);
            $transaksi2->update(['total_harga' => $hasil2->harga_per_kg * 25]);
        }

        // Transaction 3 - Dikirim
        $transaksi3 = Transaksi::create([
            'kode_transaksi' => 'TRX-' . date('YmdHis') . '-003',
            'tengkulak_id' => $tengkulak->first()->id,
            'pembeli_id' => $pembeli->count() >= 3 ? $pembeli->get(2)->id : $pembeli->first()->id,
            'tanggal_transaksi' => now()->subDays(3)->toDateString(),
            'total_harga' => 0,
            'status' => 'dikirim',
            'catatan' => 'Dalam perjalanan ke lokasi',
        ]);

        if ($hasilTangkapan->count() >= 3) {
            $hasil3 = $hasilTangkapan->get(2);
            DetailTransaksi::create([
                'transaksi_id' => $transaksi3->id,
                'hasil_tangkapan_id' => $hasil3->id,
                'jumlah_kg' => 20,
                'harga_satuan' => $hasil3->harga_per_kg,
                'subtotal' => $hasil3->harga_per_kg * 20,
            ]);
            $transaksi3->update(['total_harga' => $hasil3->harga_per_kg * 20]);
        }

        // Transaction 4 - Selesai
        $transaksi4 = Transaksi::create([
            'kode_transaksi' => 'TRX-' . date('YmdHis') . '-004',
            'tengkulak_id' => $tengkulak->count() >= 2 ? $tengkulak->get(1)->id : $tengkulak->first()->id,
            'pembeli_id' => $pembeli->first()->id,
            'tanggal_transaksi' => now()->subDays(7)->toDateString(),
            'total_harga' => 0,
            'status' => 'selesai',
            'catatan' => 'Sudah diterima pembeli',
        ]);

        if ($hasilTangkapan->count() >= 4) {
            $hasil4 = $hasilTangkapan->get(3);
            DetailTransaksi::create([
                'transaksi_id' => $transaksi4->id,
                'hasil_tangkapan_id' => $hasil4->id,
                'jumlah_kg' => 40,
                'harga_satuan' => $hasil4->harga_per_kg,
                'subtotal' => $hasil4->harga_per_kg * 40,
            ]);
            $transaksi4->update(['total_harga' => $hasil4->harga_per_kg * 40]);
        }

        // Transaction 5 - Dibatalkan
        $transaksi5 = Transaksi::create([
            'kode_transaksi' => 'TRX-' . date('YmdHis') . '-005',
            'tengkulak_id' => $tengkulak->first()->id,
            'pembeli_id' => $pembeli->count() >= 2 ? $pembeli->get(1)->id : $pembeli->first()->id,
            'tanggal_transaksi' => now()->subDays(6)->toDateString(),
            'total_harga' => 0,
            'status' => 'dibatalkan',
            'catatan' => 'Stok habis',
        ]);

        if ($hasilTangkapan->count() >= 5) {
            $hasil5 = $hasilTangkapan->get(4);
            DetailTransaksi::create([
                'transaksi_id' => $transaksi5->id,
                'hasil_tangkapan_id' => $hasil5->id,
                'jumlah_kg' => 50,
                'harga_satuan' => $hasil5->harga_per_kg,
                'subtotal' => $hasil5->harga_per_kg * 50,
            ]);
            $transaksi5->update(['total_harga' => $hasil5->harga_per_kg * 50]);
        }
    }
}
