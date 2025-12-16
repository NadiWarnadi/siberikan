<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterJenisIkan;

class MasterJenisIkanSeeder extends Seeder
{
    public function run(): void
    {
        $jenisIkan = [
            ['nama_ikan' => 'Tuna', 'nama_latin' => 'Thunnus', 'deskripsi' => 'Ikan tuna segar untuk sashimi dan steak'],
            ['nama_ikan' => 'Kakap', 'nama_latin' => 'Lutjanus', 'deskripsi' => 'Ikan kakap merah premium'],
            ['nama_ikan' => 'Kembung', 'nama_latin' => 'Rastrelliger', 'deskripsi' => 'Ikan kembung untuk konsumsi harian'],
            ['nama_ikan' => 'Bandeng', 'nama_latin' => 'Chanos chanos', 'deskripsi' => 'Ikan bandeng tanpa duri'],
            ['nama_ikan' => 'Tongkol', 'nama_latin' => 'Euthynnus', 'deskripsi' => 'Ikan tongkol segar'],
            ['nama_ikan' => 'Teri', 'nama_latin' => 'Stolephorus', 'deskripsi' => 'Ikan teri medan premium'],
            ['nama_ikan' => 'Cakalang', 'nama_latin' => 'Katsuwonus pelamis', 'deskripsi' => 'Ikan cakalang fufu'],
            ['nama_ikan' => 'Gurame', 'nama_latin' => 'Osphronemus goramy', 'deskripsi' => 'Ikan gurame budidaya'],
        ];

        foreach ($jenisIkan as $ikan) {
            MasterJenisIkan::create($ikan);
        }
    }
}
