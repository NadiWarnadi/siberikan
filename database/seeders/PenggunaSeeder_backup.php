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
            // Admin
            [
                'nama' => 'Admin Siberikan',
                'email' => 'admin@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'admin',
                'no_telepon' => '081234567890',
                'alamat' => 'Kantor Pusat Siberikan, Jakarta',
                'is_active' => true,
            ],
            // Owner
            [
                'nama' => 'Roni Owner',
                'email' => 'owner@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'owner',
                'no_telepon' => '081300000001',
                'alamat' => 'Jl. Sudirman, Jakarta Pusat',
                'is_active' => true,
            ],
            // Nelayan (3)
            [
                'nama' => 'Budi Nelayan',
                'email' => 'nelayan@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'nelayan',
                'no_telepon' => '081234567890',
                'alamat' => 'Pelabuhan Muara Baru, Jakarta Utara',
                'is_active' => true,
            ],
            [
                'nama' => 'Bambang Nelayan',
                'email' => 'bambang@nelayan.com',
                'password' => Hash::make('password123'),
                'peran' => 'nelayan',
                'no_telepon' => '081234567891',
                'alamat' => 'Kalibaru, Jakarta Utara',
                'is_active' => true,
            ],
            [
                'nama' => 'Joko Nelayan',
                'email' => 'joko@nelayan.com',
                'password' => Hash::make('password123'),
                'peran' => 'nelayan',
                'no_telepon' => '081234567892',
                'alamat' => 'Penjaringan, Jakarta Utara',
                'is_active' => true,
            ],
            // Tengkulak (2)
            [
                'nama' => 'Siti Tengkulak',
                'email' => 'tengkulak@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'tengkulak',
                'no_telepon' => '081234567893',
                'alamat' => 'Pasar Ikan Modern, Jakarta Utara',
                'is_active' => true,
            ],
            [
                'nama' => 'Hendra Tengkulak',
                'email' => 'hendra@tengkulak.com',
                'password' => Hash::make('password123'),
                'peran' => 'tengkulak',
                'no_telepon' => '081234567894',
                'alamat' => 'Blok A, Pasar Ikan, Jakarta Utara',
                'is_active' => true,
            ],
            // Pembeli (3)
            [
                'nama' => 'Dewi Pembeli',
                'email' => 'pembeli@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'pembeli',
                'no_telepon' => '081234567895',
                'alamat' => 'Jl. Raya Bogor, Jakarta Timur',
                'is_active' => true,
            ],
            [
                'nama' => 'Ani Pembeli',
                'email' => 'ani@pembeli.com',
                'password' => Hash::make('password123'),
                'peran' => 'pembeli',
                'no_telepon' => '081234567896',
                'alamat' => 'Jatinegara, Jakarta Timur',
                'is_active' => true,
            ],
            [
                'nama' => 'Indra Pembeli',
                'email' => 'indra@pembeli.com',
                'password' => Hash::make('password123'),
                'peran' => 'pembeli',
                'no_telepon' => '081234567897',
                'alamat' => 'Rawamangun, Jakarta Timur',
                'is_active' => true,
            ],
            // Sopir (3)
            [
                'nama' => 'Ahmad Sopir',
                'email' => 'sopir@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'sopir',
                'no_telepon' => '081234567898',
                'alamat' => 'Jl. Raya Cilincing, Jakarta Utara',
                'is_active' => true,
            ],
            [
                'nama' => 'Suryanto Sopir',
                'email' => 'suryanto@sopir.com',
                'password' => Hash::make('password123'),
                'peran' => 'sopir',
                'no_telepon' => '081234567899',
                'alamat' => 'Penjaringan, Jakarta Utara',
                'is_active' => true,
            ],
            [
                'nama' => 'Widi Sopir',
                'email' => 'widi@sopir.com',
                'password' => Hash::make('password123'),
                'peran' => 'sopir',
                'no_telepon' => '081234567800',
                'alamat' => 'Ancol, Jakarta Utara',
                'is_active' => true,
            ],
            // Staff (1)
            [
                'nama' => 'Tono Staff',
                'email' => 'staff@siberikan.com',
                'password' => Hash::make('password123'),
                'peran' => 'staff',
                'no_telepon' => '081234567801',
                'alamat' => 'Kantor Siberikan, Jakarta',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            Pengguna::create($user);
        }
    }
}
