<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelHistory extends Model
{
    use HasFactory;
    
    protected $fillable = ['longitude','latitude','starting_date','ending_date','user_id'];

    protected  $table = 'travel_histories';
    
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function travelMilages()
    {
        return $this->hasMany(TravelMilage::class);
    }

    public function uploaded_images()
    {
        return $this->hasMany(uploaded_images::class);
    }

    public function getTravelHistory($id)
    {
        return travel_history::find($id)->with('uploaded_images');
    }
    
    public function insertRow($data){
       return  TravelHistory::create([
            'longitude' => $data->longitude,
            'latitude' => $data->latitude,
            'starting_date' => $data->starting_date ?? null,
            'ending_date' => $data->ending_date ?? null,
            'user_id' => auth()->user()->id
        ]);
        
    }
    
    public function getAll(){
        return $this->where('user_id',auth()->user()->id)
       ->with('travelMilages')->get();
    }
}
