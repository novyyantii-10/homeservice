<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'rating', 'comment', 'is_approved'];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    // Relasi
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Accessor
    public function getRatingStarsAttribute()
    {
        // Perulangan: Generate bintang rating
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '⭐';
            } else {
                $stars .= '☆';
            }
        }
        return $stars;
    }

    public function getShortCommentAttribute()
    {
        return strlen($this->comment) > 100 
            ? substr($this->comment, 0, 100) . '...' 
            : $this->comment;
    }

    // Scope
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeHighRating($query, $minRating = 4)
    {
        return $query->where('rating', '>=', $minRating);
    }

    // Logika: Cek apakah review berkualitas tinggi
    public function isHighQuality()
    {
        return $this->rating >= 4 && strlen($this->comment) > 20;
    }
}