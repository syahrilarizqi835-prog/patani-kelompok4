@extends('layouts.app')

@section('title', 'Login - PATANI')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-green-50 via-white to-white p-4">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <a href="/" class="inline-flex items-center gap-2">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-600">
                    <i class="fas fa-seedling text-white text-2xl"></i>
                </div>
                <span class="text-3xl font-bold text-gray-800">PATANI</span>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Masuk ke Akun</h2>
                <p class="text-gray-600 mt-2">Masukkan email dan password untuk melanjutkan</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="nama@email.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="Masukkan password">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm text-green-600 hover:underline">Lupa password?</a>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-200">
                    Masuk
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="font-medium text-green-600 hover:underline">Daftar sekarang</a>
                </p>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-gray-500">
            Dengan masuk, Anda menyetujui 
            <a href="#" class="text-green-600 hover:underline">Syarat & Ketentuan</a>
            dan 
            <a href="#" class="text-green-600 hover:underline">Kebijakan Privasi</a>
        </p>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
