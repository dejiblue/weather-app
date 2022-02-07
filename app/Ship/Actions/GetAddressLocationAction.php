<?php

namespace App\Ship\Actions;

use Illuminate\Support\Facades\Log;
use App\Ship\Abstracts\Action;
use Zttp\Zttp;

class GetAddressLocationAction extends Action
{
    private const API_RESPONSE_OK = 'OK';
    /**
     * @param string $city
     * @return mixed
     */
    public function handle(string $city): array
    {
        try {
            $lat = 0;
            $lng = 0;
            $response = [];
            $apikey = config('services.googleapi.key');
            $url    = config('services.googleapi.url');

            $location_data = $url."?key=".$apikey."&address=".str_replace(" ", "+", $city)."&sensor=false";
            $data = json_decode(file_get_contents($location_data));

            if ($data->status == self::API_RESPONSE_OK) {
                $lat = $data->results[0]->geometry->location->lat;
                $lng = $data->results[0]->geometry->location->lng;

                if($lat && $lng) {
                    $response = [
                        'status' => true,
                        'lat' => $lat,
                        'long' => $lng,
                        'google_place_id' => $data->results[0]->place_id
                    ];
                }
            }

        } catch(\Exception $e) {
            Log::error($e->getMessage());
        }
        return $response;

    }
}
