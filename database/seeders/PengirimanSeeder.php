<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengirimen;
use App\Models\Transaksi;
use App\Models\Pengguna;

class PengirimanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transaksis = Transaksi::whereIn('status', ['dikemas', 'dikirim', 'selesai'])->get();
        $sopir = Pengguna::where('peran', 'sopir')->get();

        if ($transaksis->isEmpty() || $sopir->isEmpty()) {
            return;
        }

        // Pengiriman 1 - Menunggu (for dikemas transaction)
        $dikemas = $transaksis->where('status', 'dikemas')->first();
        if ($dikemas) {
            Pengirimen::create([
                'transaksi_id' => $dikemas->id,
                'sopir_id' => $sopir->first()->id,
                'nomor_resi' => 'RSI-' . date('YmdHis') . '-001',
                'tanggal_kirim' => now()->toDateString(),
                'tanggal_estimasi' => now()->addDays(2)->toDateString(),
                'tanggal_diterima' => null,
                'alamat_tujuan' => 'Jatinegara, Jakarta Timur',
                'status' => 'menunggu',
                'catatan' => 'Siap dikirim',
            ]);
        }

        // Pengiriman 2 - Dalam Perjalanan (for dikirim transaction)
        $dikirim = $transaksis->where('status', 'dikirim')->first();
        if ($dikirim) {
            Pengirimen::create([
                'transaksi_id' => $dikirim->id,
                'sopir_id' => $sopir->count() >= 2 ? $sopir->get(1)->id : $sopir->first()->id,
                'nomor_resi' => 'RSI-' . date('YmdHis') . '-002',
                'tanggal_kirim' => now()->subDays(1)->toDateString(),
                'tanggal_estimasi' => now()->addDays(1)->toDateString(),
                'tanggal_diterima' => null,
                'alamat_tujuan' => 'Rawamangun, Jakarta Timur',
                'status' => 'dalam_perjalanan',
                'catatan' => 'Sedang dalam perjalanan',
            ]);
        }

        // Pengiriman 3 - Terkirim (for selesai transaction)
        $selesai = $transaksis->where('status', 'selesai')->first();
        if ($selesai) {
            Pengirimen::create([
                'transaksi_id' => $selesai->id,
                'sopir_id' => $sopir->count() >= 3 ? $sopir->get(2)->id : $sopir->first()->id,
                'nomor_resi' => 'RSI-' . date('YmdHis') . '-003',
                'tanggal_kirim' => now()->subDays(3)->toDateString(),
                'tanggal_estimasi' => now()->subDays(1)->toDateString(),
                'tanggal_diterima' => now()->subDays(1)->toDateString(),
                'alamat_tujuan' => 'Jl. Raya Bogor, Jakarta Timur',
                'status' => 'terkirim',
                'catatan' => 'Sudah diterima penerima',
            ]);
        }

        // Pengiriman 4 - Additional for variety
        if ($transaksis->count() >= 2) {
            $transaksi4 = $transaksis->get(1);
            if ($transaksi4->status != 'dibatalkan') {
                Pengirimen::create([
                    'transaksi_id' => $transaksi4->id,
                    'sopir_id' => $sopir->first()->id,
                    'nomor_resi' => 'RSI-' . date('YmdHis') . '-004',
                    'tanggal_kirim' => now()->subDays(2)->toDateString(),
                    'tanggal_estimasi' => now()->toDateString(),
                    'tanggal_diterima' => null,
                    'alamat_tujuan' => 'Jatinegara, Jakarta Timur',
                    'status' => 'dalam_perjalanan',
                    'catatan' => 'Dalam perjalanan menuju lokasi',
                ]);
            }
        }
    }
}
