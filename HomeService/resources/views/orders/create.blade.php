@extends('layouts.app')

@section('title', 'Buat Pesanan')
@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-shopping-cart"></i> Buat Pesanan Baru</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('orders.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="service_id" class="form-label">Pilih Layanan</label>
                <select class="form-select @error('service_id') is-invalid @enderror" 
                        id="service_id" name="service_id" required>
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" 
                            {{ old('service_id', request('service_id')) == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} - {{ $service->formatted_price }}
                        </option>
                    @endforeach
                </select>
                @error('service_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Nama Customer</label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                               id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                               id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                        @error('customer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_phone" class="form-label">No. Telepon</label>
                        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                               id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="order_date" class="form-label">Tanggal Pemesanan</label>
                        <input type="date" class="form-control @error('order_date') is-invalid @enderror" 
                               id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" 
                               min="{{ date('Y-m-d') }}" required>
                        @error('order_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Alamat Lengkap</label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Preview Harga -->
            <div class="alert alert-info" id="pricePreview">
                <strong>Info:</strong> Total harga akan dihitung otomatis setelah memilih layanan dan tanggal
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Buat Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_id');
    const dateInput = document.getElementById('order_date');
    const pricePreview = document.getElementById('pricePreview');
    
    const services = @json($services->keyBy('id'));
    
    function updatePricePreview() {
        const serviceId = serviceSelect.value;
        const selectedDate = dateInput.value;
        
        if (serviceId && selectedDate) {
            const service = services[serviceId];
            let total = service.price;
            
            // Aritmatika: Hitung diskon weekend
            const date = new Date(selectedDate);
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;
            
            if (isWeekend) {
                total = total * 0.9; // Diskon 10%
            }
            
            // Aritmatika: Tambah pajak 10%
            total = total * 1.1;
            
            const formattedPrice = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(total);
            
            const discountInfo = isWeekend ? 
                ' (Termasuk diskon 10% weekend + pajak 10%)' : 
                ' (Termasuk pajak 10%)';
                
            pricePreview.innerHTML = `
                <strong>Estimasi Total:</strong> ${formattedPrice}${discountInfo}
            `;
        } else {
            pricePreview.innerHTML = '<strong>Info:</strong> Total harga akan dihitung otomatis setelah memilih layanan dan tanggal';
        }
    }
    
    serviceSelect.addEventListener('change', updatePricePreview);
    dateInput.addEventListener('change', updatePricePreview);
    
    // Trigger initial calculation if service_id is preselected from URL
    if (serviceSelect.value) {
        updatePricePreview();
    }
});
</script>
@endsection