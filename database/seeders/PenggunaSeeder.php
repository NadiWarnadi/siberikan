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
            [
                'nama' => 'Budi Nelayan',
                'email' => 'nelayan@siberikan.com',
                'password' => Hash::make('password'),
                'peran' => 'nelayan',
                'alamat' => 'Pelabuhan Muara Baru, Jakarta Utara',
            ],
            [
                'nama' => 'Siti Tengkulak',
                'email' => 'tengkulak@siberikan.com',
                'password' => Hash::make('password'),
                'peran' => 'tengkulak',
                'alamat' => 'Pasar Ikan Modern, Jakarta Utara',
            ],
            [
                'nama' => 'Ahmad Sopir',
                'email' => 'sopir@siberikan.com',
                'password' => Hash::make('password'),
                'peran' => 'sopir',
                'alamat' => 'Jl. Raya Cilincing, Jakarta Utara',
            ],
            [
                'nama' => 'Dewi Pembeli',
                'email' => 'pembeli@siberikan.com',
                'password' => Hash::make('password'),
                'peran' => 'pembeli',
                'alamat' => 'Jl. Raya Bogor, Jakarta Timur',
            ],
        ];

        foreach ($users as $user) {
            Pengguna::create($user);
        }
    }
}
