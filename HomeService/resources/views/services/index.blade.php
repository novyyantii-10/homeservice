@extends('layouts.app')

@section('title', 'Daftar Layanan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-list"></i> Daftar Layanan</h2>
    <a href="{{ route('services.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Layanan
    </a>
</div>

<div class="row">
    @foreach($services as $service)
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $service->name }}</h5>
                <p class="card-text">{{ $service->description }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 text-primary mb-0">{{ $service->formatted_price }}</span>
                    <div>
                        <a href="{{ route('orders.create') }}?service_id={{ $service->id }}" 
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-shopping-cart"></i> Pesan
                        </a>
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" 
                                    onclick="return confirm('Hapus layanan ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($services->isEmpty())
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle"></i> Belum ada layanan yang tersedia.
</div>
@endif
@endsection