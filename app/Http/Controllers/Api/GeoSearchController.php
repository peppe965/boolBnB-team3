<?php

// defining Namespace
namespace App\Http\Controllers\Api;

// using Laravel Facades
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Facade\FlareClient\Http\Response;

// using Carbon
use Carbon\Carbon;

// using Models
use App\Flat;

class GeoSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function geo_search(Request $request)
    {

        function km_distance ( $coordinate_a, $coordinate_b ) {

          list($decLatA, $decLonA) = array_map('trim', explode(',', $coordinate_a));
          list($decLatB, $decLonB) = array_map('trim', explode(',', $coordinate_b));

          $radLatA = pi() * $decLatA / 180;
          $radLonA = pi() * $decLonA / 180;
          $radLatB = pi() * $decLatB / 180;
          $radLonB = pi() * $decLonB / 180;

          $phi = abs($radLonA - $radLonB);

          $P = acos (
                      (sin($radLatA) * sin($radLatB)) + (cos($radLatA) * cos($radLatB) * cos($phi))
                    );

          return $P * 6372.795477598;
        }

        if (isset($request->latlong) && isset($request->radius)) {

          $datetime_now = Carbon::now();

          // TODO - SUGAR SINTAX
          $rooms = isset($request->rooms) ? ($request->rooms) - 1 : 0;
          $beds = isset($request->beds) ? ($request->beds) - 1 : 0;
          $bathrooms = isset($request->bathrooms) ? ($request->bathrooms) - 1 : 0;

          $flats = Flat::where('active', 1)
                       ->where('number_of_rooms', '>', $rooms)
                       ->where('number_of_beds', '>', $beds)
                       ->where('number_of_bathrooms', '>', $bathrooms)
                       ->get();

          $results = [];

          foreach ($flats as $flat) {

            $coordinate = $flat->lat.','.$flat->lng;

            $flat->distance_km = km_distance($request->latlong, $coordinate);


            if ((count($flat->sponsorships) > 0) && ($flat->sponsorships[count($flat->sponsorships) - 1]->date_of_end) > $datetime_now) {
              $flat->sponsored = true;
            } else {
              $flat->sponsored = false;
            }

            unset($flat->user_id);
            unset($flat->active);
            unset($flat->extra_options);
            unset($flat->created_at);
            unset($flat->updated_at);
            unset($flat->sponsorships);

            if ($flat->distance_km <= $request->radius) {
              array_push($results, $flat);
            }

          }

          return response()->json($results);

        } else {

            return response()->json(['error'=>'missing required parameter']);

        }

    }
}
