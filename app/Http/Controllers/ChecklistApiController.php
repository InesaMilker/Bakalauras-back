<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Trips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChecklistApiController extends Controller
{
  public function index()
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      return Checklist::where("user_id", $user_id)->get();
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function store($id)
  {
    $validator = Validator::make(request()->all(), [
      "text" => "required",
    ]);

    if ($validator->fails()) {
      return $validator->errors();
    }

    $isGuest = auth()->guest();

    if (!$isGuest) {
      if (Trips::where("id", $id)->exists()) {
        $user_id = auth()->user()->id;
        if (Trips::where("id", $id)->first()->user_id == $user_id) {
          return Checklist::create([
            "text" => request("text"),
            "trip_id" => $id,
            "user_id" => $user_id,
          ]);
        } else {
          return response()->json(["message" => "Trip not found"], 404);
        }
      } else {
        return response()->json(["message" => "Trip not found"], 404);
      }
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function update(Request $request, $id)
  {
    if (Checklist::where("id", $id)->exists()) {
      $isGuest = auth()->guest();

      if (!$isGuest) {
        $user_id = auth()->user()->id;
        $checklist = Checklist::find($id);

        if ($user_id == $checklist->user_id) {
          $checklist->state = is_null($request->state)
            ? $checklist->state
            : $request->state;
          $checklist->text = is_null($request->text)
            ? $checklist->text
            : $request->text;
          $checklist->user_id = $checklist->user_id;
          $checklist->trip_id = $checklist->trip_id;
          $checklist->save();

          return response()->json(
            [
              "message" => "Checklist updated successfully",
              "ckecklist" => $checklist,
            ],
            200
          );
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
      } else {
        return response()->json(["message" => "Unauthorized"], 401);
      }
    } else {
      return response()->json(["message" => "Checklist not found"], 404);
    }
  }

  public function destroy($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;

      if (Checklist::where("id", $id)->exists()) {
        $checklist = Checklist::find($id);

        if ($user_id == $checklist->user_id) {
          $checklist->delete();

          return response()->json(["message" => "Checklist deleted"], 202);
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
      } else {
        return response()->json(["message" => "Checklist not found"], 404);
      }
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function wanted($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $checklist = Checklist::find($id);

      if ($user_id == $checklist->user_id) {
        if (Checklist::where("id", $id)->exists()) {
          return Checklist::find($id);
        } else {
          return response()->json(
            [
              "message" => "Checklist not found",
            ],
            404
          );
        }
      } else {
        return response()->json(["message" => "Unauthorized"], 401);
      }
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }
}
