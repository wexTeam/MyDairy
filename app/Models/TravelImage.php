<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelImage extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function travelMilages()
    {
        return $this->belongsTo(TravelHistory::class);
    }
}
