@extends('layouts.app')

@section('title', 'Dean Dashboard')

@section('content')
    <div class="bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-bold mb-4">Dashboard Trưởng Khoa</h2>
        <p>Chào mừng {{ Auth::user()->name }}! Đây là dashboard tổng quan dành cho Trưởng Khoa.</p>
    </div>
@endsection