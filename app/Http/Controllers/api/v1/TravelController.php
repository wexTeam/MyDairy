<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Traits\TravelChecks;
use App\Models\TravelHistory;
use App\Models\TravelMilage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Validator;

class TravelController extends BaseAPIController
{
    use TravelChecks;

    public function createTravelHistory(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => ['nullable', 'numeric', 'exists:travel_histories,id'],
            'latitude' => ['required', 'numeric', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'numeric', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'address' => ['nullable','string']
        ]);
        if ($validator->fails()) {
            return $this->errorJsonReponse('Travel History not set', $validator->errors());
        }

        if (empty($request->get('id'))) {

            $travelHistory = (new TravelHistory())->insertRow((object)$request->all());

            //update starting date
            $this->updateTravelStartingDate($travelHistory);

            return $this->successJsonReponse($travelHistory);

        } else {

            $travelHistory = $this->insertAccordingRule($request);

            return $this->successJsonReponse($travelHistory);
        }
    }

    public function insertAccordingRule($request)
    {
        $travelHistory = TravelHistory::find($request->get('id'));

        // cal distance btw 2 points
        $distance = $this->distance($travelHistory->latitude, $travelHistory->longitude, $request->latitude, $request->longitude);

        if ($distance < config('setting.insertTravelDistance')) {

            (new TravelMilage())->insertRow((object) $request->all());

        } else {

            //update ending date
            $this->updateTravelEndingDate($travelHistory);

            $travelHistory = (new TravelHistory())->insertRow((object)$request->all());

            //update new entry starting date
            $this->updateTravelStartingDate($travelHistory);
        }

        return $travelHistory;
    }

    public function updateTravelEndingDate($travelHistory){
        $travelHistory->ending_date = \Carbon\Carbon::now();
        $travelHistory->save();
    }

    public function updateTravelStartingDate($travelHistory){
        $travelHistory->starting_date = \Carbon\Carbon::now();
        $travelHistory->save();
    }

    public function getAll(){

        $travelHistories = (new TravelHistory())->getAll();

        return $this->successJsonReponse($travelHistories);
    }
    
    public function getDashboardTravelHistory(){

        return $this->successJsonReponse([
            'today' => $this->getTodayTravelingDistance() .' miles',
            'yesterday' => $this->getYesterdayTravelHistory()
        ]);
    }
    
    public function getTodayTravelingDistance(){

        $travelHistories = (new TravelHistory())->travelHistoryByDate(Carbon::now());
        $totalDistance = 0;
        $startLat = $startLong = '';

       foreach ($travelHistories as $travelHistory){

           // check if first time set param else add in total idstance
           if(empty($startLat)){
               $startLat = $travelHistory->latitude;
               $startLong = $travelHistory->longitude;
           }else{
               $totalDistance += $this->distance($startLat, $startLong, $travelHistory->latitude, $travelHistory->longitude,'N');
               $startLat = $travelHistory->latitude;
               $startLong = $travelHistory->longitude;
           }


           $travelMiages = $travelHistory->travelMilages;

           // if have milages cal from milages else from next travel history
           if(!$travelMiages->isEmpty()){

             foreach ($travelMiages as $travelMiage){
                 $totalDistance += $this->distance($startLat, $startLong, $travelMiage->latitude, $travelMiage->longitude,'N');
                 $startLat = $travelMiage->latitude;
                 $startLong = $travelHistory->longitude;
             }
           }
       }

        return $totalDistance;

    }

    public function getYesterdayTravelHistory(){
        $returnData = [];
        $travelHistories = (new TravelHistory())->travelHistoryByDate(Carbon::now()->subDay(1));

        foreach ($travelHistories as $travelHistory){
           $tempData = [];
            $startTime = Carbon::parse($travelHistory->starting_date);
            $endTime = Carbon::parse($travelHistory->ending_date);
            $tempData['time'] = $startTime->diff($endTime)->format('%H:%I:%S');

            $tempData['no_of_images'] = $travelHistory->travelImages->count();

            $tempData['address'] = $travelHistory->address;

            $tempData['id'] = $travelHistory->id;

            array_push($returnData,$tempData);
        }

        return $returnData;
    }

    public function getLastTravelHistory(){
        $returnData = [];
        
        $travelHistories = (new TravelHistory())->travelHistoryByDate(Carbon::now()->subDay(1));

        foreach ($travelHistories as $travelHistory){
            $tempData = [];
            $startTime = Carbon::parse($travelHistory->starting_date);
            $endTime = Carbon::parse($travelHistory->ending_date);
            $tempData['time'] = $startTime->diff($endTime)->format('%H:%I:%S');

            $tempData['no_of_images'] = $travelHistory->travelImages->count();

            $tempData['address'] = $travelHistory->address;

            $tempData['id'] = $travelHistory->id;

            array_push($returnData,$tempData);
        }
        
        return $this->successJsonReponse($returnData);
    }
}
