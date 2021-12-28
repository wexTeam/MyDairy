<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelMilage extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function travelMilages()
    {
        return $this->belongsTo(TravelHistory::class);
    }
    
    public function insertRow($data){
       return TravelMilage::create([
            'longitude' => $data->longitude,
            'latitude' => $data->latitude,
            'travel_history_id' => $data->id
        ]);
        
    }
}
