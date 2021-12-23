<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class uploaded_images extends Model
{
    use HasFactory;

    public function travelMilages()
    {
        return $this->belongsTo(travel_history::class, 'th_id');
    }
}
