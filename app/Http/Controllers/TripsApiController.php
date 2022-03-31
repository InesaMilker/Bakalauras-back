<?php

namespace App\Http\Controllers;

use App\Models\Trips;
use Illuminate\Http\Request;


class TripsApiController extends Controller
{
    public function index()
    {
        $isGuest = auth()->guest();
        
        if(!$isGuest)
        {
            $user_id = auth()->user()->id;
            return Trips::where('user_id', $user_id)->get();
        }
        else
        {
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function store()
    {
        request()->validate(['title' => 'required', 
        'start_date' => 'required', 'end_date' => 'required']);

        $isGuest = auth()->guest();

        if (!$isGuest) {
            $user_id = auth()->user()->id;

            return Trips::create(['title' => request('title'),
             'start_date' => request('start_date'), 
             'end_date' => request('end_date'), 'user_id' => $user_id]);
        } 
        else 
        {
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function update(Request $request, $id)
    {
        request()->validate(['rating' => 'numeric|between:1,8', ]);

        if (Trips::where('id', $id)->exists()) 
        {
            $isGuest = auth()->guest();

            if (!$isGuest) 
            {
                $user_id = auth()->user()->id;
                $user_role = auth()->user()->role;

                if (Trips::where('id', $id)->exists())
                {
                    $trips = Trips::find($id);

                    if ($user_id == $trips->user_id || $user_role == 1) {
                        $trips->title = is_null($request->title) 
                        ? $trips->title : $request->title;
                        $trips->start_date = is_null($request->start_date) 
                        ? $trips->start_date : $request->start_date;
                        $trips->end_date = is_null($request->end_date) 
                        ? $trips->end_date : $request->end_date;
                        $trips->rating = is_null($request->rating) 
                        ? $trips->rating : $request->rating;
                        $trips->user_id = $trips->user_id;
                        $trips->save();

                        return response()
                        ->json(["message" => "Trip updated successfully", "outfit" => $trips], 401);
                    } 
                    
                    else 
                    {
                        return response()->json(["message" => "Unauthorized"], 401);
                    }
                } 
                
                else 
                {
                    return response()
                        ->json(["message" => "Trip not found"], 404);
                }
            } else 
            {
                return response()
                    ->json(["message" => "Unauthorized"], 401);
            }
        } 
        else 
        {
            return response()->json(["message" => "Trip not found"], 404);
        }
    }

    public function destroy($id)
    {
        $isGuest = auth()->guest();

        if (!$isGuest) {

            $user_id = auth()->user()->id;
            $user_role = auth()->user()->role;

            if (Trips::where('id', $id)->exists()) {

                $trips = Trips::find($id);

                if ($user_id == $trips->user_id || $user_role == 1) {

                    $trips->delete();

                    return response()
                        ->json(["message" => "Trip deleted"], 202);
                } 
                else {
                    return response()
                        ->json(["message" => "Unauthorized"], 401);
                }
            } 
            else {
                return response()
                    ->json(["message" => "Trip not found"], 404);
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

        if (!$isGuest) {

            $user_id = auth()->user()->id;
            $user_role = auth()->user()->role;
            $trips = Trips::find($id);

            if ($user_id == $trips->user_id || $user_role == 1) {

                if (Trips::where('id', $id)->exists()) {
                    return Trips::find($id);
                } 
                else {
                    return response()->json([
                        "message" => "Trip not found",
                    ], 404);
                }
            }
            else {
                return response()
                    ->json(["message" => "Unauthorized"], 401);
            }
        } 
        else {
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
}
