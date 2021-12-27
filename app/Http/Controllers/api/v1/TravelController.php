<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelHistoryRequest;
use App\Models\travel_history;
use App\Models\travel_milage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TravelController extends BaseAPIController
{
    //
    public function createTravelHistory(TravelHistoryRequest $request)
    {
        $id = $request->get('id');
        if ($id == 0) {
            /**
             * Create the Travel History of User if id comes first as a 0 (Parent Table)
             */
            $travel_history = travel_history::create([
                'longitude' => $request->get('longitude'),
                'latitude' => $request->get('latitude'),
                'starting_date' => $request->get('starting_date'),
                'user_id' => auth()->user()->id
            ]);
            return $this->responseJSON($travel_history);
        } else {
            //Extracting longitude and latitude from database of current id and calculating Distance
            $calculated_distance = $this->total_distance_in_by_km($id);
            if ($calculated_distance > 5) {
                $travel_history = travel_history::create([
                    'longitude' => $request->get('longitude'),
                    'latitude' => $request->get('latitude'),
                    'ending_date' => $request->get('ending_date'),
                    'user_id' => auth()->user()->id
                ]);
                return $this->responseJSON($travel_history);
            } else {
                $distance = $this->total_distance_in_by_km($id);
                if ($distance > 5) {
                    $travel_history = travel_history::create([
                        'longitude' => $request->longtitude,
                        'latitude' => $request->latitude

                    ]);
                    return $this->responseJSON($travel_history);
                } else {
                    $travel_milage = travel_milage::create([
                        'longitude' => $request->longitude,
                        'latitude' => $request->latitude,
                        'th_id' => $id
                    ]);
                    return $this->responseJSON($travel_milage);
                }
            }
        }
    }
    private function total_distance_in_by_km($id = 0)
    {
        $totalMiles = 0.0;
        $todayDate = Carbon::today()->toDateString();
        $travel_history = travel_history::where('id', $id)->first();

        $sltlg = array();
        $sltlg['lat'] = $travel_history['latitude'];
        $sltlg['lng'] = $travel_history['longitude'];

        $mlatlong = array();
        array_push($mlatlong, $sltlg);

        if (!($travel_history) == null) {
            $travel_milages = travel_milage::where('th_id', $travel_history['id'])->get();
            foreach ($travel_milages as $travel_milage) {
                $multiplelatlong = array();
                $multiplelatlong['lat'] = $travel_milage['latitude'];
                $multiplelatlong['lng'] = $travel_milage['longitude'];
                array_push($mlatlong, $multiplelatlong);
            }

            $count = travel_milage::where('th_id', $travel_history['id'])->count();

            //return $carMilage;

            if ($count > 0) {
                for ($i = 0; $i < count($mlatlong); $i++) {
                    if (isset($mlatlong[$i + 1]['lng'])) {
                        $theta = $mlatlong[$i]['lng'] - $mlatlong[$i + 1]['lng'];
                        $dist = sin(deg2rad($mlatlong[$i]['lat']))
                            *
                            sin(deg2rad($mlatlong[$i + 1]['lat']))
                            +
                            cos(deg2rad($mlatlong[$i]['lat']))
                            * cos(deg2rad($mlatlong[$i + 1]['lat']))
                            * cos(deg2rad($theta));
                        $dist = acos($dist);
                        $dist = rad2deg($dist);

                        $miles = $dist * 60 *  1.1515;

                        $kilometers = round($miles * 1.609344, PHP_ROUND_HALF_UP) . ' KM';
                        //$totalMiles=$totalMiles+round($miles,2);
                    }
                }
                return round($kilometers, 2);
                //return round($miles,2);}
            } else {
                return 'Incomplete Travel history';
            }
        }
    }
}
