<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Request;
use App\Models\Trips;
use Illuminate\Support\Facades\Auth;

class DiaryApiController extends Controller
{
  public function index()
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      return Diary::where("user_id", $user_id)->get();
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function store()
  {
    request()->validate(["title" => "required", "content" => "required"]);

    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;

      if (
        Trips::where("id", request("trip_id"))->first()->user_id == $user_id
      ) {
        return Diary::create([
          "title" => request("title"),
          "content" => request("content"),
          "user_id" => $user_id,
          "trip_id" => request("trip_id"),
        ]);
      } else {
        return response()->json(["message" => "Trip not found"], 404);
      }
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function update(Request $request, $id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $user_role = auth()->user()->role;

      if (Diary::where("id", $id)->exists()) {
        $diary = Diary::find($id);

        if ($user_id == $diary->user_id || $user_role == 1) {
          $diary->title = is_null($request->title)
            ? $diary->title
            : $request->title;
          $diary->content = is_null($request->content)
            ? $diary->content
            : $request->content;
          $diary->user_id = $diary->user_id;
          $diary->trip_id = $diary->trip_id;
          $diary->save();

          return response()->json(
            ["message" => "Dairy updated successfully", "diary" => $diary],
            200
          );
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
      } else {
        return response()->json(["message" => "Diary not found"], 404);
      }
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function destroy($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $user_role = auth()->user()->role;

      if (Diary::where("id", $id)->exists()) {
        $diary = Diary::find($id);

        if ($user_id == $diary->user_id || $user_role == 1) {
          $diary->delete();

          return response()->json(["message" => "Diary deleted"], 202);
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
      } else {
        return response()->json(["message" => "Diary not found"], 404);
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
      $user_role = auth()->user()->role;
      $diary = Diary::find($id);

      if ($user_id == $diary->user_id || $user_role == 1) {
        if (Diary::where("id", $id)->exists()) {
          return Diary::find($id);
        } else {
          return response()->json(
            [
              "message" => "Dairy not found",
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
