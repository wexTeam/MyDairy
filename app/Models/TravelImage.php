<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelImage extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function getImagePathAttribute($value)
    {
        return url('uploads/'.$value);
    }

    public function setImagePathAttribute($value)
    {
        $this->attributes['image_path'] =  url('uploads/'.$value);
    }


    public function travelMilages()
    {
        return $this->belongsTo(TravelHistory::class);
    }

    public function insertRow($data){

        return  TravelImage::create([
            'travel_history_id' => $data->id,
            'image_path' => $data->image_path,

        ]);
    }

    public function getAll(){
        $travelHistoryIds = auth()->user()->travelHistories->pluck('id');

        return $this->whereIn('travel_history_id', $travelHistoryIds)
            ->orderBy('created_at', 'desc')
            ->paginate(config('setting.pagination'));
    }

    public function getTravelHistoryImages($id){
        
        return $this->where('travel_history_id',$id)->get();
    }
    public function del($id){

        return $this->findOrFail($id)->delete();
    }
}
