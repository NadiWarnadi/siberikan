<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penawaran;
use App\Models\Pengguna;
use App\Models\MasterJenisIkan;

class PenawaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get nelayan users
        $nelayan = Pengguna::where('peran', 'nelayan')->get();
        $jenisIkan = MasterJenisIkan::all();

        if ($nelayan->isEmpty() || $jenisIkan->isEmpty()) {
            return;
        }

        $penawarans = [];
        
        // Nelayan 1 offerings
        if ($nelayan->isNotEmpty()) {
            $nelayan1 = $nelayan->first();
            
            $penawarans[] = [
                'kode_penawaran' => 'PNW-' . date('YmdHis') . '-001',
                'nelayan_id' => $nelayan1->id,
                'jenis_ikan_id' => $jenisIkan->count() > 0 ? $jenisIkan->first()->id : 1,
                'jumlah_kg' => 100,
                'harga_per_kg' => 50000,
                'kualitas' => 'A',
                'lokasi_tangkapan' => 'Laut Jawa, Jakarta',
                'kedalaman' => '50 meter',
                'tanggal_tangkapan' => now()->subDays(5)->toDateString(),
                'catatan' => 'Tuna segar berkualitas premium dari tangkapan hari ini',
                'foto_ikan' => null,
                'status' => 'approved',
                'approved_by' => null,
                'approved_at' => now()->subDays(4),
            ];
            
            $penawarans[] = [
                'kode_penawaran' => 'PNW-' . date('YmdHis') . '-002',
                'nelayan_id' => $nelayan1->id,
                'jenis_ikan_id' => $jenisIkan->count() > 1 ? $jenisIkan->get(1)->id : 1,
                'jumlah_kg' => 80,
                'harga_per_kg' => 45000,
                'kualitas' => 'A',
                'lokasi_tangkapan' => 'Laut Jawa, Jakarta',
                'kedalaman' => '60 meter',
                'tanggal_tangkapan' => now()->subDays(3)->toDateString(),
                'catatan' => 'Salmon impor segar berkualitas tinggi',
                'foto_ikan' => null,
                'status' => 'approved',
                'approved_by' => null,
                'approved_at' => now()->subDays(2),
            ];
            
            $penawarans[] = [
                'kode_penawaran' => 'PNW-' . date('YmdHis') . '-003',
                'nelayan_id' => $nelayan1->id,
                'jenis_ikan_id' => $jenisIkan->count() > 2 ? $jenisIkan->get(2)->id : 1,
                'jumlah_kg' => 50,
                'harga_per_kg' => 55000,
                'kualitas' => 'A',
                'lokasi_tangkapan' => 'Laut Jawa, Jakarta',
                'kedalaman' => '40 meter',
                'tanggal_tangkapan' => now()->subDays(2)->toDateString(),
                'catatan' => 'Udang besar segar dari laut dalam',
                'foto_ikan' => null,
                'status' => 'approved',
                'approved_by' => null,
                'approved_at' => now()->subDays(1),
            ];
        }

        // Nelayan 2 offerings
        if ($nelayan->count() >= 2) {
            $nelayan2 = $nelayan->get(1);
            
            $penawarans[] = [
                'kode_penawaran' => 'PNW-' . date('YmdHis') . '-004',
                'nelayan_id' => $nelayan2->id,
                'jenis_ikan_id' => $jenisIkan->count() > 3 ? $jenisIkan->get(3)->id : 1,
                'jumlah_kg' => 150,
                'harga_per_kg' => 25000,
                'kualitas' => 'B',
                'lokasi_tangkapan' => 'Kolam Budidaya, Bekasi',
                'kedalaman' => '3 meter',
                'tanggal_tangkapan' => now()->subDays(4)->toDateString(),
                'catatan' => 'Tilapia segar ukuran sedang',
                'foto_ikan' => null,
                'status' => 'approved',
                'approved_by' => null,
                'approved_at' => now()->subDays(3),
            ];
            
            $penawarans[] = [
                'kode_penawaran' => 'PNW-' . date('YmdHis') . '-005',
                'nelayan_id' => $nelayan2->id,
                'jenis_ikan_id' => $jenisIkan->count() > 4 ? $jenisIkan->get(4)->id : 1,
                'jumlah_kg' => 200,
                'harga_per_kg' => 20000,
                'kualitas' => 'B',
                'lokasi_tangkapan' => 'Kolam Budidaya, Tangerang',
                'kedalaman' => '2 meter',
                'tanggal_tangkapan' => now()->subDays(1)->toDateString(),
                'catatan' => 'Lele air tawar berkualitas bagus',
                'foto_ikan' => null,
                'status' => 'approved',
                'approved_by' => null,
                'approved_at' => now(),
            ];
            
            $penawarans[] = [
                'kode_penawaran' => 'PNW-' . date('YmdHis') . '-006',
                'nelayan_id' => $nelayan2->id,
                'jenis_ikan_id' => $jenisIkan->count() > 2 ? $jenisIkan->get(2)->id : 1,
                'jumlah_kg' => 120,
                'harga_per_kg' => 35000,
                'kualitas' => 'A',
                'lokasi_tangkapan' => 'Laut Jawa, Jakarta',
                'kedalaman' => '45 meter',
                'tanggal_tangkapan' => now()->subDays(6)->toDateString(),
                'catatan' => 'Kakap merah segar dari pasar ikan',
                'foto_ikan' => null,
                'status' => 'approved',
                'approved_by' => null,
                'approved_at' => now()->subDays(5),
            ];
        }

        // Nelayan 3 offerings (if exists)
        if ($nelayan->count() >= 3) {
            $nelayan3 = $nelayan->get(2);
            
            $penawarans[] = [
                'kode_penawaran' => 'PNW-' . date('YmdHis') . '-007',
                'nelayan_id' => $nelayan3->id,
                'jenis_ikan_id' => $jenisIkan->count() > 5 ? $jenisIkan->get(5)->id : 1,
                'jumlah_kg' => 180,
                'harga_per_kg' => 22000,
                'kualitas' => 'B',
                'lokasi_tangkapan' => 'Kolam Budidaya, Depok',
                'kedalaman' => '2.5 meter',
                'tanggal_tangkapan' => now()->subDays(2)->toDateString(),
                'catatan' => 'Patin segar dari budidaya terpercaya',
                'foto_ikan' => null,
                'status' => 'approved',
                'approved_by' => null,
                'approved_at' => now()->subDays(1),
            ];
            
            $penawarans[] = [
                'kode_penawaran' => 'PNW-' . date('YmdHis') . '-008',
                'nelayan_id' => $nelayan3->id,
                'jenis_ikan_id' => $jenisIkan->count() > 3 ? $jenisIkan->get(3)->id : 1,
                'jumlah_kg' => 110,
                'harga_per_kg' => 38000,
                'kualitas' => 'A',
                'lokasi_tangkapan' => 'Laut Jawa, Jakarta',
                'kedalaman' => '50 meter',
                'tanggal_tangkapan' => now()->subDays(3)->toDateString(),
                'catatan' => 'Tenggiri premium hasil tangkapan laut',
                'foto_ikan' => null,
                'status' => 'approved',
                'approved_by' => null,
                'approved_at' => now()->subDays(2),
            ];
            
            $penawarans[] = [
                'kode_penawaran' => 'PNW-' . date('YmdHis') . '-009',
                'nelayan_id' => $nelayan3->id,
                'jenis_ikan_id' => $jenisIkan->count() > 1 ? $jenisIkan->get(1)->id : 1,
                'jumlah_kg' => 140,
                'harga_per_kg' => 30000,
                'kualitas' => 'B',
                'lokasi_tangkapan' => 'Laut Jawa, Jakarta',
                'kedalaman' => '40 meter',
                'tanggal_tangkapan' => now()->subDays(7)->toDateString(),
                'catatan' => 'Bawal segar ukuran medium',
                'foto_ikan' => null,
                'status' => 'approved',
                'approved_by' => null,
                'approved_at' => now()->subDays(6),
            ];
        }

        // Insert all penawarans
        foreach ($penawarans as $penawaran) {
            Penawaran::create($penawaran);
        }
    }
}
