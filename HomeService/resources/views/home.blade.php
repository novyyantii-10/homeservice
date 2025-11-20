@extends('layouts.app')

@section('title', 'Home')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="jumbotron bg-light p-5 rounded">
            <h1 class="display-4">Selamat Datang di Home Service</h1>
            <p class="lead">Layanan jasa perbaikan dan perawatan rumah profesional untuk kebutuhan Anda.</p>
            <hr class="my-4">
            <p>Pilih dari berbagai layanan kami dan pesan sekarang!</p>
            <a class="btn btn-primary btn-lg" href="{{ route('services.index') }}" role="button">
                Lihat Layanan <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistik</h5>
            </div>
            <div class="card-body">
                <p><strong>Total Pesanan:</strong> {{ $totalOrders }}</p>
                <p><strong>Pesanan Selesai:</strong> {{ $completedOrders }}</p>
                <p><strong>Rate Penyelesaian:</strong> {{ number_format($completionRate, 1) }}%</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <h3>Layanan Populer</h3>
        <div class="row">
            @foreach($services as $service)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->name }}</h5>
                        <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                        <p class="text-primary fw-bold">{{ $service->formatted_price }}</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('orders.create') }}?service_id={{ $service->id }}" 
                           class="btn btn-outline-primary btn-sm">
                            Pesan Sekarang
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection