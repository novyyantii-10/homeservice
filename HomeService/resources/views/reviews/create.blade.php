@extends('layouts.app')

@section('title', 'Buat Review')
@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-star"></i> Beri Review</h4>
    </div>
    <div class="card-body">
        <!-- Order Info -->
        <div class="alert alert-info">
            <h5>Informasi Pesanan</h5>
            <p><strong>Layanan:</strong> {{ $order->service->name }}</p>
            <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
            <p><strong>Tanggal Pesanan:</strong> {{ $order->order_date->format('d/m/Y') }}</p>
        </div>

        <form action="{{ route('reviews.store') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            
            <div class="mb-4">
                <label class="form-label">Rating</label>
                <div class="rating-stars">
                    {{-- Perulangan: Buat 5 bintang rating --}}
                    @for($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" 
                               {{ old('rating') == $i ? 'checked' : '' }} required>
                        <label for="star{{ $i }}">
                            <i class="fas fa-star"></i>
                        </label>
                    @endfor
                </div>
                @error('rating')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="comment" class="form-label">Komentar</label>
                <textarea class="form-control @error('comment') is-invalid @enderror" 
                          id="comment" name="comment" rows="4" 
                          placeholder="Bagaimana pengalaman Anda dengan layanan kami?" 
                          required>{{ old('comment') }}</textarea>
                @error('comment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Kirim Review
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 5px;
}

.rating-stars input {
    display: none;
}

.rating-stars label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.rating-stars input:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #ffc107;
}

.rating-stars input:checked + label {
    color: #ffc107;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-stars input');
    const labels = document.querySelectorAll('.rating-stars label');
    
    stars.forEach(star => {
        star.addEventListener('change', function() {
            const rating = this.value;
            labels.forEach((label, index) => {
                if (5 - index <= rating) {
                    label.style.color = '#ffc107';
                } else {
                    label.style.color = '#ddd';
                }
            });
        });
    });
});
</script>
@endsection