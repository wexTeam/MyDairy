<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelHistoryRequest;
use App\Models\travel_history;
use Illuminate\Http\Request;

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
            //Extracting row from database where t
        }
    }
}
