@extends('layouts.app')

@section('title', 'Login - SIBERIKAN')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Login SIBERIKAN</h4>
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

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <p class="text-center mb-0">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
                </p>

                <div class="mt-3">
                    <small class="text-muted">
                        <strong>web si percobaan warnadi</strong><br>
                        
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
