<?php

namespace App\Http\Controllers;

use App\Models\Outfit;
use Illuminate\Http\Request;

class OutfitsApiController extends Controller
{
    public function index()
    {
        $isGuest = auth()->guest();
        
        if(!$isGuest)
        {
            $user_id = auth()->user()->id;
            return Outfit::where('user_id', $user_id)->get();
        }
        else
        {
            return response()->json(["message" => "Unauthorized"], 401);
        };
    }

    public function store(Request $request)
    {

        request()->validate(['outfit_name' => 'required', 'outfit_image' => 'required',]);
        
        $isGuest = auth()->guest();
        
        if(!$isGuest)
        {
            $user_id = auth()->user()->id;

            if($request->hasFile('outfit_image')) {
                $image = $request->file('outfit_image');
                $filename = time().rand(1,3). '.'.$image->getClientOriginalName();
                $image->move('uploads/', $filename);

                return Outfit::create(['outfit_name' => request('outfit_name'), 'outfit_image' => $filename, 'user_id' => $user_id,]);
            }
            return Outfit::create(['outfit_name' => request('outfit_name'), 'outfit_image'=>"", 'user_id' => $user_id, ]); 
        }
        else
        {
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if(Outfit::where('id', $id)->exists())
        {
            $isGuest = auth()->guest();

            if(!$isGuest)
            {
                $user_id=auth()->user()->id;
                $user_role = auth()->user()->role;

                if(Outfit::where('id', $id)->exists())
                {
                    $outfit = Outfit::find($id);

                    if($user_id == $outfit->user_id || $user_role == 1)
                    {
                        $outfit->outfit_name = is_null($request->outfit_name) ? $outfit->outfit_name : $request->outfit_name;
                        $outfit->outfit_image = is_null($request->outfit_image) ? $outfit->outfit_image : $request->outfit_image;
                        $outfit->user_id = $outfit->user_id;
                        $outfit->save();

                        return response()->json(["message" => "Outfit updated successfully", "outfit" => $outfit], 401);
                    }
                    else
                    {
                        return response()->json(["message" => "Unauthorized"], 401);
                    }
                }
                else
                {
                    return response()
                        ->json(["message" => "Outfit not found"], 404);
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
            return response()->json(["message" => "Outfit not found"], 404);
        }
    }

    
    public function destroy($id)
    {
        $isGuest = auth()->guest();

        if (!$isGuest)
        {

            $user_id = auth()->user()->id;
            $user_role = auth()->user()->role;

            if (Outfit::where('id', $id)->exists())
            {

                $outfit = Outfit::find($id);

                //Checks if its current users post or its an admin trying to delete.
                if ($user_id == $outfit->user_id || $user_role == 1)
                {

                    $outfit->delete();

                    return response()
                        ->json(["message" => "outfit deleted"], 202);
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
                    ->json(["message" => "outfit not found"], 404);
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
            $outfit = Outfit::find($id);

            if ($user_id == $outfit->user_id || $user_role == 1) {

                if (Outfit::where('id', $id)->exists()) {
                    return Outfit::find($id);
                } 
                else {
                    return response()->json([
                        "message" => "Outfit not found",
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
