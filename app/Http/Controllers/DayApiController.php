<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Trips;

class DayApiController extends Controller
{
    public function index()
    {
        return Day::all();

    }

    public function store()
    {
        $isGuest = auth()->guest();

        if (!$isGuest)
        {
            if (Trips::where('id', request('trip_id'))->exists())
            {
                return Day::create(['day_number' => request('day_number') , 'trip_id' => request('trip_id'), 'budget' => request('budget'), 'note' => request('note'), ]);
            }
            else
            {
                return response()->json(["message" => "Post not found"], 404);
            }
        }
        else
        {
            return response()
            ->json(["message" => "Unauthorized"], 401);
        }
    }
}
