@extends('layouts.app')

@section('title', 'Admin - Tambah Pengguna')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Pengguna Baru</h1>
        <p class="text-gray-600 mt-2">Isi semua field yang diperlukan untuk membuat pengguna baru</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Ada {{ $errors->count() }} kesalahan</h3>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-lg shadow-md p-6">
        @csrf

        <!-- Nama -->
        <div class="mb-6">
            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-600">*</span></label>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                class="w-full px-4 py-2 border @error('nama') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Masukkan nama lengkap">
            @error('nama')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-600">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                class="w-full px-4 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="nama@example.com">
            @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-600">*</span></label>
            <input type="password" id="password" name="password" required
                class="w-full px-4 py-2 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Minimal 8 karakter">
            @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Confirmation -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password <span class="text-red-600">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Ulangi password">
        </div>

        <!-- Role -->
        <div class="mb-6">
            <label for="peran" class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-600">*</span></label>
            <select id="peran" name="peran" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Role --</option>
                <option value="nelayan" {{ old('peran') === 'nelayan' ? 'selected' : '' }}>Nelayan (Penjual Ikan)</option>
                <option value="tengkulak" {{ old('peran') === 'tengkulak' ? 'selected' : '' }}>Tengkulak (Persetujuan)</option>
                <option value="pembeli" {{ old('peran') === 'pembeli' ? 'selected' : '' }}>Pembeli</option>
                <option value="sopir" {{ old('peran') === 'sopir' ? 'selected' : '' }}>Sopir (Pengiriman)</option>
                <option value="staff" {{ old('peran') === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="owner" {{ old('peran') === 'owner' ? 'selected' : '' }}>Owner (Manajemen Pesanan)</option>
                <option value="admin" {{ old('peran') === 'admin' ? 'selected' : '' }}>Admin (Superuser)</option>
            </select>
            @error('peran')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- No. Telepon -->
        <div class="mb-6">
            <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
            <input type="tel" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}"
                class="w-full px-4 py-2 border @error('no_telepon') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="08xxxxxxxxxx">
            @error('no_telepon')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alamat -->
        <div class="mb-6">
            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3"
                class="w-full px-4 py-2 border @error('alamat') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
            @error('alamat')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Is Active -->
        <div class="mb-6">
            <label for="is_active" class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1" 
                    {{ old('is_active') ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-700">Aktifkan pengguna langsung</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                Tambah Pengguna
            </button>
            <a href="{{ route('admin.users.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-4 rounded-lg text-center">
                Batal
            </a>
        </div>
    </form>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-medium text-blue-900 mb-2">Informasi</h3>
        <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
            <li>Password minimal 8 karakter</li>
            <li>Email harus unik dan belum terdaftar</li>
            <li>Nomor telepon untuk komunikasi pengguna</li>
            <li>Role menentukan akses dan fitur yang tersedia</li>
        </ul>
    </div>
</div>
@endsection
