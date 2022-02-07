<?php

namespace App\Ship\Actions;

use App\Ship\Abstracts\Action;
use Zttp\Zttp;

class GetWeatherForecastAction extends Action
{
    /**
     * @param float $lat
     * @param float $lng
     * @return mixed
     */
    public function handle(float $lat, float $lng)
    {
        $apikey = config('services.openweathermap.key');

        $response = Zttp::get("https://api.openweathermap.org/data/2.5/onecall?lat=$lat&lon=$lng&units=metric
                    exclude=hourly,daily&appid=$apikey");

        return $response->json();

    }
}
