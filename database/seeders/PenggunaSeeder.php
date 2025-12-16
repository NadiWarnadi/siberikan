<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Nelayan (2)
            [
                'nama' => 'Budi Nelayan',
                'email' => 'nelayan1@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'nelayan',
                'alamat' => 'Pelabuhan Muara Baru, Jakarta Utara',
            ],
            [
                'nama' => 'Bambang Nelayan',
                'email' => 'nelayan2@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'nelayan',
                'alamat' => 'Kalibaru, Jakarta Utara',
            ],
            // Tengkulak (2)
            [
                'nama' => 'Siti Tengkulak',
                'email' => 'tengkulak@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'tengkulak',
                'alamat' => 'Pasar Ikan Modern, Jakarta Utara',
            ],
            [
                'nama' => 'Hendra Tengkulak',
                'email' => 'tengkulak2@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'tengkulak',
                'alamat' => 'Blok A, Pasar Ikan, Jakarta Utara',
            ],
            // Pembeli (2)
            [
                'nama' => 'Dewi Pembeli',
                'email' => 'pembeli1@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'pembeli',
                'alamat' => 'Jl. Raya Bogor, Jakarta Timur',
            ],
            [
                'nama' => 'Ani Pembeli',
                'email' => 'pembeli2@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'pembeli',
                'alamat' => 'Jatinegara, Jakarta Timur',
            ],
            // Sopir (2)
            [
                'nama' => 'Ahmad Sopir',
                'email' => 'sopir1@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'sopir',
                'alamat' => 'Jl. Raya Cilincing, Jakarta Utara',
            ],
            [
                'nama' => 'Suryanto Sopir',
                'email' => 'sopir2@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'sopir',
                'alamat' => 'Penjaringan, Jakarta Utara',
            ],
        ];

        foreach ($users as $user) {
            Pengguna::create($user);
        }
    }
}
