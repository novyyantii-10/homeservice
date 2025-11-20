<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id', 'customer_name', 'customer_email', 'customer_phone',
        'address', 'order_date', 'order_time', 'total_price', 'status', 'notes'
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    // Relasi
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // Accessor
    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getOrderDateTimeAttribute()
    {
        return $this->order_date->format('d/m/Y') . ' ' . $this->order_time;
    }

    // Logika: Cek apakah bisa memberikan review
    public function canAddReview()
    {
        return $this->status == 'completed' && !$this->review;
    }

    // Logika: Cek apakah order bisa dibatalkan
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    // Aritmatika: Hitung total harga dengan berbagai faktor
    public function calculateTotalPrice()
    {
        $basePrice = $this->service->price;
        
        // Diskon berdasarkan hari
        $dayDiscount = $this->calculateDayDiscount();
        
        // Diskon berdasarkan waktu (pagi lebih murah)
        $timeDiscount = $this->calculateTimeDiscount();
        
        // Pajak layanan
        $tax = $basePrice * 0.1; // 10% pajak
        
        $total = $basePrice - $dayDiscount - $timeDiscount + $tax;
        
        return max($total, $basePrice * 0.5); // Minimal 50% dari harga normal
    }

    // Aritmatika: Diskon berdasarkan hari
    private function calculateDayDiscount()
    {
        $dayOfWeek = $this->order_date->dayOfWeek;
        
        // Senin-Jumat diskon 5%, Weekend normal
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            return $this->service->price * 0.05; // 5% discount
        }
        
        return 0;
    }

    // Aritmatika: Diskon berdasarkan waktu
    private function calculateTimeDiscount()
    {
        $hour = (int) date('H', strtotime($this->order_time));
        
        // Pagi (08:00-11:00) diskon 10%
        if ($hour >= 8 && $hour <= 11) {
            return $this->service->price * 0.1; // 10% discount
        }
        
        return 0;
    }

    // Scope
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }
}