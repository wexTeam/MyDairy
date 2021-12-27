<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class travel_history extends Model
{
    use HasFactory;
    protected $table = 'travel_histories';
    protected $guarded = [];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function travelMilages()
    {
        return $this->hasMany(travel_milage::class);
    }

    public function uploaded_images()
    {
        return $this->hasMany(uploaded_images::class);
    }

    public function getTravelHistory($id)
    {
        return travel_history::find($id)->with('uploaded_images');
    }
}
