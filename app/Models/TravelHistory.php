<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TravelHistory extends Model
{
    use HasFactory;
    
    protected $fillable = ['longitude','latitude','starting_date','ending_date','user_id','address'];

    protected  $table = 'travel_histories';
    
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function travelMilages()
    {
        return $this->hasMany(TravelMilage::class);
    }

    public function travelImages()
    {
        return $this->hasMany(TravelImage::class);
    }

    public function getTravelHistory($id)
    {
        return TravelHistory::find($id)->with('travelImages');
    }
    
    public function insertRow($data){
       return  TravelHistory::create([
            'longitude' => $data->longitude,
            'latitude' => $data->latitude,
            'starting_date' => $data->starting_date ?? null,
            'ending_date' => $data->ending_date ?? null,
            'user_id' => auth()->user()->id,
            'address' => $data->address ?? null
        ]);
    }
    
    public function getAll(){
        return $this->where('user_id',auth()->user()->id)
       ->with('travelMilages')->get();
    }

    public function travelHistoryByDate($date){
        return $this->where('user_id',auth()->user()->id)
            ->whereDate('starting_date',$date)
            ->with(['travelMilages','travelImages'])->get();
    }
}
