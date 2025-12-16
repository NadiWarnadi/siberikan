@extends('layouts.app')

@section('title', 'Register - SIBERIKAN')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0"><i class="bi bi-person-plus"></i> Registrasi Pengguna Baru</h4>
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" 
                               value="{{ old('nama') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="peran" class="form-label">Peran</label>
                        <select class="form-select" id="peran" name="peran" required>
                            <option value="">-- Pilih Peran --</option>
                            <option value="nelayan" {{ old('peran') == 'nelayan' ? 'selected' : '' }}>Nelayan</option>
                            <option value="sopir" {{ old('peran') == 'sopir' ? 'selected' : '' }}>Sopir</option>
                            <option value="pembeli" {{ old('peran') == 'pembeli' ? 'selected' : '' }}>Pembeli</option>
                        </select>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Peran Tengkulak hanya diberikan oleh admin
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Daftar
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <p class="text-center mb-0">
                    Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
