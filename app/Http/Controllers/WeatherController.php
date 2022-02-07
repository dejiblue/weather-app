<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Ship\Actions\GetWeatherForecastAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class WeatherController
 * @package App\Http\Controllers
 */
class WeatherController extends Controller
{

    /**
     * Get weather Data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $response = null;
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        try {
            $response = app()->make(GetWeatherForecastAction::class)
                             ->handle($lat, $lng);

        } catch(\Exception $e) {
            Log::error($e->getMessage());
        }

        if ($response) {
            return response()->json($response);
        }
        return response()->json(['success' => false], 404);
    }

}
