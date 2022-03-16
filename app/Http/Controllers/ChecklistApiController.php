<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistApiController extends Controller
{
    public function index()
    {
        return Checklist::all();
    }

    public function store()
    {

        request()->validate(['text' => 'required',]);
        
        $isGuest = auth()->guest();
        
        if(!$isGuest)
        {
            $user_id = auth()->user()->id;

            return Checklist::create(['text' => request('text'), 'user_id' => $user_id, ]);
        }
        else
        {
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if(Checklist::where('id', $id)->exists())
        {
            $isGuest = auth()->guest();

            if(!$isGuest)
            {
                $user_id=auth()->user()->id;
                $user_role = auth()->user()->role;

                if(Checklist::where('id', $id)->exists())
                {
                    $ckecklist = Checklist::find($id);

                    if($user_id == $ckecklist->user_id || $user_role == 1)
                    {
                        $ckecklist->text = is_null($request->text) ? $ckecklist->text : $request->text;
                        $ckecklist->user_id = $ckecklist->user_id;
                        $ckecklist->save();

                        return response()->json(["message" => "Ckecklist updated successfully", "ckecklist" => $ckecklist], 401);
                    }
                    else
                    {
                        return response()->json(["message" => "Unauthorized"], 401);
                    }
                }
                else
                {
                    return response()
                        ->json(["message" => "Ckecklist not found"], 404);
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
            return response()->json(["message" => "Ckecklist not found"], 404);
        }
    }

    
    public function destroy($id)
    {
        $isGuest = auth()->guest();

        if (!$isGuest)
        {

            $user_id = auth()->user()->id;
            $user_role = auth()->user()->role;

            if (Checklist::where('id', $id)->exists())
            {

                $checklist = Checklist::find($id);

                //Checks if its current users post or its an admin trying to delete.
                if ($user_id == $checklist->user_id || $user_role == 1)
                {

                    $checklist->delete();

                    return response()
                        ->json(["message" => "checklist deleted"], 202);
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
                    ->json(["message" => "checklist not found"], 404);
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
        $isGuest = auth()->guest();

        if (!$isGuest) {

            $user_id = auth()->user()->id;
            $user_role = auth()->user()->role;
            $checklist = Checklist::find($id);

            if ($user_id == $checklist->user_id || $user_role == 1) {

                if (Checklist::where('id', $id)->exists()) {
                    return Checklist::find($id);
                } 
                else {
                    return response()->json([
                        "message" => "Checklist not found",
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
