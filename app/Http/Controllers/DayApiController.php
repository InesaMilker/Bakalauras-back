<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Trips;
use Illuminate\Http\Request;

class DayApiController extends Controller
{
    public function index()
    {
        $isGuest = auth()->guest();
        
        if(!$isGuest)
        {
            $user_id = auth()->user()->id;
            return Day::where('user_id', $user_id)->get();
        }
        else
        {
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function store()
    {
        $isGuest = auth()->guest();

        if (!$isGuest) {

            if (Trips::where('id', request('trip_id'))->exists()) {
                return Day::create(['day_number' => request('day_number'), 'trip_id' => request('trip_id'), 'budget' => request('budget'), 'note' => request('note'),]);
            } 
            else {
                return response()->json(["message" => "Trip not found"], 404);
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
            if (Day::where('id', $id)->exists()) {
                $day = Day::find($id);
                $day->day_number = $day->day_number;
                $day->trip_id = $day->trip_id;
                $day->budget = is_null($request->budget) ? $day->budget : $request->budget;
                $day->note = is_null($request->note) ? $day->note : $request->note;
                $day->save();

                return response()
                    ->json(["message" => "Day updated successfully"], 200);
            } 
            else {
                return response()
                    ->json(["message" => "Day not found"], 404);
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
            if (Day::where('id', $id)->exists()) {
                $day = Day::find($id);
                $day->delete();

                return response()
                    ->json(["message" => "Day deleted"], 202);
            } 
            else {
                return response()
                    ->json(["message" => "Day not found"], 404);
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
            if(Day::where('id', $id)->exists())
            {
                return Day::find($id);
            }
            else
            {
                return response()->json([
                    "message" => "Day not found"
                ], 404);
            }
        } 
        else 
        {
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
}
