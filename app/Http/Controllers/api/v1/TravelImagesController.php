<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\TravelHistory;
use App\Models\TravelImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use File;

class TravelImagesController extends BaseAPIController
{
    public function uploadTravelImage(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => ['nullable', 'numeric', 'exists:travel_histories,id'],
            'image' => 'required|mimes:jpeg,png,jpg'
        ]);
        if ($validator->fails()) {
            return $this->errorJsonReponse('Travel History not set', $validator->errors());
        }
        //upload image
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $file = $request->file('image');
                $destinationPath = public_path('/uploads');
                //$extension = $file->getClientOriginalExtension('logo');
                $image = $file->getClientOriginalName('image');
                $image = rand().$image;
                $request->file('image')->move($destinationPath, $image);
                $input["image_path"] = $image;
            }
        }
        //insert record
        $travelImage = (new TravelImage())->insertRow((object)$input);

        return $this->successJsonReponse($travelImage);

    }

    public function getAll(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => ['nullable', 'numeric', 'exists:travel_histories,id'],
        ]);
        if ($validator->fails()) {
            return $this->errorJsonReponse('Travel History not set', $validator->errors());
        }
        $travelImage = (new TravelImage())->getAll($request->id);
        return $this->successJsonReponse($travelImage);
    }

    public function delete(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => ['nullable', 'numeric', 'exists:travel_images,id'],
        ]);
        if ($validator->fails()) {
            return $this->errorJsonReponse('Travel Image not set', $validator->errors());
        }
        $travelImage = (new TravelImage())->del($request->id);
        return $this->successJsonReponse($travelImage);
    }
}
