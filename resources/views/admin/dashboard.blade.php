@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<nav class="nk-navbar">
    <div class="container py-2 d-flex align-items-center justify-content-between">
        <a class="nk-brand" href="{{ url('/admin/dashboard') }}">
            <img src="{{ asset('images/logo/logo-ngekosyuk.jpeg') }}"
                 alt="Logo ngeKosYuk"
                 width="34"
                 height="34"
                 class="nk-brand-logo">
            <span>ngeKosYuk</span>
            <span class="nk-brand-role">Admin</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="small fw-medium text-secondary">{{ auth()->user()->name }}</span>
            <form action="{{ url('/logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn-nk-secondary py-1 px-3 small">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="nk-page-header">
        <div>
            <h1 class="nk-page-title">Dashboard Admin</h1>
            <p class="nk-page-subtitle">Selamat datang, {{ auth()->user()->name }}. Pantau aktivitas pengguna dan operasional properti ngeKosYuk.</p>
        </div>
    </div>

    <div class="nk-card p-4">
        <h2 class="nk-card-title mb-2">Status Sistem</h2>
        <p class="text-secondary small mb-0">Layanan manajemen kos, pengajuan sewa, dan verifikasi pembayaran aktif beroperasi.</p>
    </div>
</div>
@endsection
