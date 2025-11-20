<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('order.service')->latest()->get();
        
        // Aritmatika: Hitung rata-rata rating
        $averageRating = $reviews->avg('rating');
        
        return view('reviews.index', compact('reviews', 'averageRating'));
    }

    public function create($order_id)
    {
        $order = Order::with('service')->findOrFail($order_id);
        
        // Logika: Cek apakah boleh kasih review
        if (!$order->canAddReview()) {
            return redirect()->back()->with('error', 'Tidak dapat memberikan review untuk pesanan ini.');
        }
        
        return view('reviews.create', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10'
        ]);

        Review::create($request->all());

        return redirect()->route('reviews.index')
            ->with('success', 'Review berhasil ditambahkan!');
    }
}