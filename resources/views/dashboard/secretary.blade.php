@extends('layouts.app')

@section('title', 'Secretary Dashboard')

@section('content')
    <div class="bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-bold mb-4">Dashboard Thư ký/Giáo vụ</h2>
        <p>Chào mừng {{ Auth::user()->name }}! Đây là giao diện quản lý hành chính/học vụ.</p>
        <!-- Add administrative content later -->
    </div>
@endsection