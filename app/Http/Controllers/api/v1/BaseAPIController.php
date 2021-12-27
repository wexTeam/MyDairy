<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class BaseAPIController extends Controller
{
    protected function responseJSON($apiResponseArray, $code = 200)
    {
        $result =  $this->encodeJson($apiResponseArray);
        return response()->json($result, $code);
    }

    public function successJsonReponse($data=[])
    {
        return response()->json([
            'status'=>config('setting.apiStatus.true'),
            'message'=>config('setting.message.status.ok'),
            'data' => $data
        ], 200);
    }

    public function errorJsonReponse($message,$data=[],$code =200)
    {

        return response()->json([
            'status'=>config('setting.apiStatus.false'),
            'message'=>$message,
            'data' => $data], 200);
        
    }


    /**
     * Encode a value to camelCase JSON
     */
    private function encodeJson($value)
    {
        if ($value instanceof Arrayable) {
            return $this->encodeJson($value->toArray());
        } else if (is_array($value)) {
            return $this->encodeArray($value);
        } else if (is_object($value)) {
            return $this->encodeArray((array) $value);
        } else {
            return $value;
        }
    }

    private function encodeArray($array)
    {
        $newArray = [];
        foreach ($array as $key => $val) {
            $newArray[Str::camel($key)] = $this->encodeJson($val);
        }
        return $newArray;
    }
}
