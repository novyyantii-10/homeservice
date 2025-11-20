@extends('layouts.app')

@section('title', 'Detail Pesanan')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-file-invoice"></i> Detail Pesanan #{{ $order->id }}</h4>
            </div>
            <div class="card-body">
                <!-- Info Customer -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Informasi Customer</h5>
                        <p><strong>Nama:</strong> {{ $order->customer_name }}</p>
                        <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                        <p><strong>Telepon:</strong> {{ $order->customer_phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Detail Pesanan</h5>
                        <p><strong>Tanggal:</strong> {{ $order->order_date_time }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : 
                                                  ($order->status == 'cancelled' ? 'danger' : 
                                                  ($order->status == 'in_progress' ? 'warning' : 'info')) }}">
                                {{ $order->status }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Info Layanan -->
                <div class="mb-4">
                    <h5>Layanan yang Dipesan</h5>
                    <div class="card">
                        <div class="card-body">
                            <h6>{{ $order->service->name }}</h6>
                            <p class="mb-1">{{ $order->service->description }}</p>
                            <p class="mb-0"><strong>Kategori:</strong> {{ $order->service->category }}</p>
                            <p class="mb-0"><strong>Durasi:</strong> {{ $order->service->duration }} jam</p>
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="mb-4">
                    <h5>Alamat Service</h5>
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-0">{{ $order->address }}</p>
                        </div>
                    </div>
                </div>

                @if($order->notes)
                <div class="mb-4">
                    <h5>Catatan Tambahan</h5>
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Review -->
                @if($order->review)
                <div class="mb-4">
                    <h5>Review Customer</h5>
                    <div class="card">
                        <div class="card-body">
                            <div class="text-warning mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $order->review->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                                <span class="ms-2">({{ $order->review->rating }}/5)</span>
                            </div>
                            <p class="mb-0">{{ $order->review->comment }}</p>
                            <small class="text-muted">
                                Diberikan pada: {{ $order->review->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Breakdown Harga -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-receipt"></i> Breakdown Harga</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Harga Dasar</td>
                        <td class="text-end">Rp {{ number_format($priceBreakdown['base_price'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Diskon Hari</td>
                        <td class="text-end text-success">-Rp {{ number_format($priceBreakdown['day_discount'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Diskon Waktu</td>
                        <td class="text-end text-success">-Rp {{ number_format($priceBreakdown['time_discount'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Pajak (10%)</td>
                        <td class="text-end">+Rp {{ number_format($priceBreakdown['tax'], 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-primary">
                        <td><strong>Total Final</strong></td>
                        <td class="text-end"><strong>Rp {{ number_format($priceBreakdown['final_price'], 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
                
                <!-- Aritmatika: Info tambahan -->
                @php
                    $savings = $priceBreakdown['day_discount'] + $priceBreakdown['time_discount'];
                    $savingPercentage = $priceBreakdown['base_price'] > 0 ? 
                                       ($savings / $priceBreakdown['base_price']) * 100 : 0;
                @endphp
                
                @if($savings > 0)
                <div class="alert alert-success mt-3">
                    <i class="fas fa-piggy-bank"></i> 
                    Anda hemat <strong>Rp {{ number_format($savings, 0, ',', '.') }}</strong> 
                    ({{ number_format($savingPercentage, 1) }}%) dari harga normal!
                </div>
                @endif
            </div>
        </div>

        <!-- Aksi -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cogs"></i> Aksi</h5>
            </div>
            <div class="card-body">
                <!-- Update Status -->
                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="mb-3">
                    @csrf
                    <label class="form-label">Update Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="in_progress" {{ $order->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>

                <!-- Tombol Aksi -->
                <div class="d-grid gap-2">
                    @if($order->canAddReview())
                    <a href="{{ route('reviews.create', $order->id) }}" class="btn btn-success">
                        <i class="fas fa-star"></i> Beri Review
                    </a>
                    @endif
                    
                    @if($order->canBeCancelled())
                    <form action="{{ route('orders.update-status', $order) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-danger w-100" 
                                onclick="return confirm('Batalkan pesanan ini?')">
                            <i class="fas fa-times"></i> Batalkan Pesanan
                        </button>
                    </form>
                    @endif
                    
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection