<?php

namespace App\Http\Controllers;

use App\Models\Coordinates;
use App\Models\Day;
use Illuminate\Http\Request;

class CoordinatesApiController extends Controller
{
    public function index()
    {
        return Coordinates::all();
    }

    public function store()
    {
        $isGuest = auth()->guest();
        $user_id = auth()->user()->id;

        if (!$isGuest) 
        {
            if (Day::where('id', request('day_id'))->first()->user_id == $user_id) 
            {
                return Coordinates::create(['location_name' => request('location_name'), 'lat' => request('lat'), 'lng' => request('lng'), 'day_id' => request('day_id'), 'user_id' => $user_id]);
            } 
            else {
                return response()->json(["message" => "Day not found"], 404);
            }
        } 
        else {
            return response()
                ->json(["message" => "Unauthorized"], 401);
        }
    }

    public function update(Request $request, $id)
    {

        $isGuest = auth()->guest();

        if (!$isGuest) {
            if (Coordinates::where('id', $id)->exists()) {
                $cordinates = Coordinates::find($id);
                $cordinates->location_name = is_null($request->location_name) ? $cordinates->location_name : $request->location_name;
                $cordinates->lat = is_null($request->lat) ? $cordinates->lat : $request->lat;
                $cordinates->lng = is_null($request->lng) ? $cordinates->lng : $request->lng;
                $cordinates->day_id = $cordinates->day_id;
                $cordinates->user_id = $cordinates->user_id;
                $cordinates->save();

                return response()
                    ->json(["message" => "Coordonate updated successfully"], 200);
            } 
            else {
                return response()
                    ->json(["message" => "Coordonate not found"], 404);
            }
        } 
        else {
            return response()
                ->json(["message" => "Unauthorized"], 401);
        }
    }

    public function destroy($id)
    {

        $isGuest = auth()->guest();
        if (!$isGuest) {
            if (Coordinates::where('id', $id)->exists()) {
                $coordinate = Coordinates::find($id);
                $coordinate->delete();

                return response()
                    ->json(["message" => "Coordinates deleted"], 202);
            } 
            else {
                return response()
                    ->json(["message" => "Coordinates not found"], 404);
            }
        } 
        else {
            return response()
                ->json(["message" => "Unauthorized"], 401);
        }
    }

    public function wanted($id)
    {
        $isGuest = auth()->guest();

        if (!$isGuest) 
        {
            if(Coordinates::where('id', $id)->exists())
            {
                return Coordinates::find($id);
            }
            else
            {
                return response()->json([
                    "message" => "Coordinates not found"
                ], 404);
            }
        } 
        else 
        {
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
    
}
