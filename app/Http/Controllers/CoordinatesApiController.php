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

        request()->validate(['location_name' => 'required', 'lat' => 'required', 'lng' => 'required',]);
        
        $isGuest = auth()->guest();
        
        if(!$isGuest)
        {
            $user_id = auth()->user()->id;

            return Diary::create(['title' => request('title'), 'content'=>request('content'), 'user_id' => $user_id, ]);
        }
        else
        {
            return response()->json(["message" => "Unauthorized"], 401);
        }
        
    }

    public function update(Request $request, $id)
    {
        if(Diary::where('id', $id)->exists())
        {
            $isGuest = auth()->guest();

            if(!$isGuest)
            {
                $user_id=auth()->user()->id;
                $user_role = auth()->user()->role;

                if(Diary::where('id', $id)->exists())
                {
                    $diary = Diary::find($id);

                    if($user_id == $diary->user_id || $user_role == 1)
                    {
                        $diary->title = is_null($request->title) ? $diary->title : $request->title;
                        $diary->content = is_null($request->content) ? $diary->content : $request->content;
                        $diary->user_id = $diary->user_id;
                        $diary->save();

                        return response()->json(["message" => "Dairy updated successfully", "diary" => $diary], 401);
                    }
                    else
                    {
                        return response()->json(["message" => "Unauthorized"], 401);
                    }
                }
                else
                {
                    return response()
                        ->json(["message" => "Diary not found"], 404);
                }           
            }
            else
            {
                return response()
                    ->json(["message" => "Unauthorized"], 401);
            }
        }
        else
        {
            return response()->json(["message" => "Diary not found"], 404);
        }
    }

    
    public function destroy($id)
    {
        $isGuest = auth()->guest();

        if (!$isGuest)
        {

            $user_id = auth()->user()->id;
            $user_role = auth()->user()->role;

            if (Diary::where('id', $id)->exists())
            {

                $diary = Diary::find($id);

                //Checks if its current users post or its an admin trying to delete.
                if ($user_id == $diary->user_id || $user_role == 1)
                {

                    $diary->delete();

                    return response()
                        ->json(["message" => "Diary deleted"], 202);
                }

                else
                {
                    return response()
                        ->json(["message" => "Unauthorized"], 401);
                }

            }
            else
            {
                return response()
                    ->json(["message" => "Diary not found"], 404);
            }
        }
        else
        {
            return response()
                ->json(["message" => "Unauthorized"], 401);
        }
    }

    public function wanted($id)
    {
        if(Diary::where('id', $id)->exists())
        {
            return Diary::find($id);
        }
        else{
            return response()->json([
                "message" => "Diary not found"
            ], 404);
        }

    }
}
