@extends('layouts.app')

@section('title', 'Daftar Review')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-star"></i> Daftar Review</h2>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>Rating Rata-rata</h4>
                <h1>{{ number_format($averageRating, 1) }}/5.0</h1>
                <div class="mt-2">
                    @php
                        $fullStars = floor($averageRating);
                        $halfStar = $averageRating - $fullStars >= 0.5;
                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                    @endphp
                    
                    {{-- Perulangan: Tampilkan bintang --}}
                    @for($i = 0; $i < $fullStars; $i++)
                        <i class="fas fa-star"></i>
                    @endfor
                    
                    @if($halfStar)
                        <i class="fas fa-star-half-alt"></i>
                    @endif
                    
                    @for($i = 0; $i < $emptyStars; $i++)
                        <i class="far fa-star"></i>
                    @endfor
                    
                    <span class="ms-2">({{ $reviews->count() }} reviews)</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reviews List -->
<div class="row">
    @foreach($reviews as $review)
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title">{{ $review->order->service->name }}</h5>
                    <div class="text-warning">
                        {{-- Perulangan: Tampilkan rating stars --}}
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                </div>
                
                <p class="card-text">{{ $review->comment }}</p>
                
                <div class="mt-auto">
                    <small class="text-muted">
                        <strong>Customer:</strong> {{ $review->order->customer_name }} | 
                        <strong>Tanggal:</strong> {{ $review->created_at->format('d/m/Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($reviews->isEmpty())
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle"></i> Belum ada review.
</div>
@endif
@endsection