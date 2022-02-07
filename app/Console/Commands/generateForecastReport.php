<?php

namespace App\Console\Commands;

use App\Ship\Actions\GetAddressLocationAction;
use App\Ship\Actions\GetWeatherForecastAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class generateForecastReport extends Command
{
    const DEFAULT_CITIES = 'lagos,new-york,sydney,melbourne,dubai';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:weather-data {--cities=}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accepts comma seperated list of cities and returns 5 days forecast data';

    /**git
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cityString = $this->option('cities');
        if (!$cityString) {
            $cityString = self::DEFAULT_CITIES;
        }

        try {
            $allCities = explode(',', $cityString);
            foreach ($allCities as $city) {
                // get the longitude and latitude
                $response = app()->make(GetAddressLocationAction::class)
                                 ->handle(trim($city));

                // use the long and lat to get 5 days weather forecast
                if ($response) {
                    $response = app()->make(GetWeatherForecastAction::class)
                                     ->handle($response['lat'], $response['long']);

                    $this->info('|=================== '.$city.' =====================|');

                    //Five day weather forecast
                    $dataCount = 0;
                    $headers = ['Day', 'description', 'min', 'max'];
                    $data = [];
                    foreach ($response['daily'] as $dailyForecast) {
                        $dataCount+=1;
                        $dayOfWeek         = date("D M j",$dailyForecast['dt']);
                        $dayDescription    = $dailyForecast['weather'][0]['description'];
                        $maxDayTemperature = round($dailyForecast['temp']['max']-273.15).' °C';
                        $minDayTemperature = round($dailyForecast['temp']['min']-273.15).' °C';
                        $data[] = [
                            'day' => $dayOfWeek,
                            'description' => $dayDescription,
                            'Min Temperature' => $maxDayTemperature,
                            'Max Temperature' => $minDayTemperature
                        ];
                        if ($dataCount >= 5) {
                            $this->table($headers, $data);
                            break;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->info($e->getMessage());
        }

    }
}
