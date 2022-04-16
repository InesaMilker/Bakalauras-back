<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Request;
use App\Models\Trips;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
    request()->validate([
      "title" => "required",
      "content" => "required",
      "date" => "required",
      "trip_id" => "required",
    ]);

    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;

      if (Trips::where("id", request("trip_id"))->exists()) {
        $start_date = Trips::where("id", request("trip_id"))->first()
          ->start_date;
        $end_date = Trips::where("id", request("trip_id"))->first()->end_date;

        if (
          Trips::where("id", request("trip_id"))->first()->user_id == $user_id
        ) {
          if (request("date") >= $start_date && request("date") <= $end_date) {
            return Diary::create([
              "title" => request("title"),
              "content" => request("content"),
              "date" => request("date"),
              "user_id" => $user_id,
              "trip_id" => request("trip_id"),
            ]);
          } else {
            return response()->json(
              [
                "message" => "Date is not in the trip date range",
              ],
              405
            );
          }
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
          $diary->date = is_null($request->date)
            ? $diary->date
            : $request->date;
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
              "message" => "Diary not found",
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

  public function tripDiariesSingle($trip_id, $diary_id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $diary = Diary::find($diary_id);
      $trip = Trips::find($trip_id);

      if ($user_id == $diary->user_id) {
        if ($user_id == $trip->user_id) {
          if (Trips::where("id", $trip_id)->exists()) {
            if (Diary::where("id", $diary_id)->exists()) {
              return Diary::find($diary_id);
            } else {
              return response()->json(
                [
                  "message" => "Diary not found",
                ],
                404
              );
            }
          } else {
            return response()->json(
              [
                "message" => "Trip not found",
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
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }
}
