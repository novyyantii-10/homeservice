@extends('layouts.app')

@section('title', 'Daftar Pesanan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-shopping-cart"></i> Daftar Pesanan</h2>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Pesan Baru
    </a>
</div>

<!-- Stats -->
<div class="row mb-4">
    @foreach($stats as $status => $count)
    <div class="col-md-3 mb-3">
        <div class="card text-white 
            @if($status == 'completed') bg-success
            @elseif($status == 'in_progress') bg-warning
            @elseif($status == 'pending') bg-info
            @else bg-danger @endif">
            <div class="card-body text-center">
                <h5 class="card-title text-uppercase">{{ str_replace('_', ' ', $status) }}</h5>
                <h2 class="card-text">{{ $count }}</h2>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Customer</th>
                        <th>Layanan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $order->customer_name }}</strong><br>
                            <small>{{ $order->customer_phone }}</small>
                        </td>
                        <td>{{ $order->service->name }}</td>
                        <td>{{ $order->order_date->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge 
                                @if($order->status == 'completed') bg-success
                                @elseif($order->status == 'in_progress') bg-warning
                                @elseif($order->status == 'pending') bg-info
                                @else bg-danger @endif">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $order->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                                @if($order->canAddReview())
                                <a href="{{ route('reviews.create', $order->id) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-star"></i> Review
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($orders->isEmpty())
<div class="alert alert-info text-center mt-4">
    <i class="fas fa-info-circle"></i> Belum ada pesanan.
</div>
@endif
@endsection