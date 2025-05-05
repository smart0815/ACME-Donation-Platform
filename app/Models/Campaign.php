<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'goal_amount',
        'end_date',
        'image_url',
        'status',
        'creator_id',
    ];

    protected $casts = [
        'goal_amount' => 'decimal:2',
        'end_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function getTotalDonationsAttribute()
    {
        return $this->donations->sum('amount');
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->goal_amount == 0) return 0;
        return min(100, ($this->total_donations / $this->goal_amount) * 100);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}