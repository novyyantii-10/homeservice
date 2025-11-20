<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'duration', 'category', 'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    // Relasi
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Accessor
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getDurationTextAttribute()
    {
        return $this->duration . ' jam';
    }

    // Scope
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Aritmatika: Hitung harga setelah diskon
    public function calculateDiscountedPrice($discountPercent = 0)
    {
        return $this->price - ($this->price * $discountPercent / 100);
    }

    // Logika: Cek apakah layanan populer berdasarkan jumlah pesanan
    public function isPopular()
    {
        $popularThreshold = 5; // Minimal 5 pesanan
        return $this->orders()->count() >= $popularThreshold;
    }
}