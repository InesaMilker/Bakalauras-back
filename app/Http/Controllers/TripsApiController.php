<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Day;
use App\Models\Diary;
use App\Models\Trips;
use Illuminate\Http\Request;
use DateTime;

class TripsApiController extends Controller
{
  public function index()
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      return Trips::where("user_id", $user_id)
        ->get()
        ->sortByDesc("start_date")
        ->values();
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function store()
  {
    request()->validate([
      "title" => "required",
      "start_date" => "required",
      "end_date" => "required",
      "place_id" => "required",
    ]);

    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;

      return Trips::create([
        "title" => request("title"),
        "start_date" => request("start_date"),
        "end_date" => request("end_date"),
        "place_id" => request("place_id"),
        "user_id" => $user_id,
      ]);
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function update(Request $request, $id)
  {
    request()->validate(["rating" => "numeric|between:1,5"]);

    if (Trips::where("id", $id)->exists()) {
      $isGuest = auth()->guest();

      if (!$isGuest) {
        $user_id = auth()->user()->id;

        if (Trips::where("id", $id)->exists()) {
          $trips = Trips::find($id);
          if (!(is_null($request->start_date) && is_null($request->end_date))) {
            $start_old = $trips->start_date;
            $start_new = $request->start_date;
            $datetime1_start = new DateTime($start_old);
            $datetime2_start = new DateTime($start_new);
            $interval_start = $datetime1_start->diff($datetime2_start);

            if ($interval_start->invert == "1") {
              $old_start_days = Day::where("trip_id", $id)->get();
              foreach ($old_start_days as $old_day) {
                $old_day->day_number =
                  $old_day->day_number + $interval_start->days;
                $old_day->save();
              }
            }

            if (
              $interval_start->invert == "0" &&
              $interval_start->days != "0"
            ) {
              $old_start_days = Day::where("trip_id", $id)->get();
              foreach ($old_start_days as $old_day) {
                if ($old_day->day_number - $interval_start->days <= 0) {
                  $old_day->delete();
                  return;
                }
                $old_day->day_number =
                  $old_day->day_number - $interval_start->days;
                $old_day->save();
              }
            }

            $end_old = $trips->end_date;
            $end_new = $request->end_date;
            $datetime1_end = new DateTime($end_old);
            $datetime2_end = new DateTime($end_new);
            $interval_end = $datetime1_end->diff($datetime2_end);

            $interval_of_new_dates = $datetime2_start->diff($datetime2_end);

            if ($interval_end->invert == "1") {
              $old_end_days = Day::where("trip_id", $id)->get();
              foreach ($old_end_days as $old_day) {
                if (
                  $old_day->day_number - ($interval_of_new_dates->days + 1) >
                  0
                ) {
                  $old_day->delete();
                }
              }
            }
          }
          if ($user_id == $trips->user_id) {
            $trips->title = is_null($request->title)
              ? $trips->title
              : $request->title;
            $trips->start_date = is_null($request->start_date)
              ? $trips->start_date
              : $request->start_date;
            $trips->end_date = is_null($request->end_date)
              ? $trips->end_date
              : $request->end_date;
            $trips->rating = is_null($request->rating)
              ? $trips->rating
              : $request->rating;
            $trips->place_id = is_null($request->place_id)
              ? $trips->place_id
              : $request->place_id;
            $trips->user_id = $trips->user_id;
            $trips->save();

            return response()->json(
              ["message" => "Trip updated successfully", "outfit" => $trips],
              200
            );
          } else {
            return response()->json(["message" => "Unauthorized"], 401);
          }
        } else {
          return response()->json(["message" => "Trip not found"], 404);
        }
      } else {
        return response()->json(["message" => "Unauthorized"], 401);
      }
    } else {
      return response()->json(["message" => "Trip not found"], 404);
    }
  }

  public function destroy($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;

      if (Trips::where("id", $id)->exists()) {
        $trips = Trips::find($id);

        if ($user_id == $trips->user_id) {
          $trips->delete();

          return response()->json(["message" => "Trip deleted"], 202);
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
      } else {
        return response()->json(["message" => "Trip not found"], 404);
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
      $trips = Trips::find($id);

      if ($user_id == $trips->user_id) {
        if (Trips::where("id", $id)->exists()) {
          return Trips::find($id);
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
  }

  public function tripChecklist($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $trip = Trips::find($id);

      if ($user_id == $trip->user_id) {
        if (Trips::where("id", $id)->exists()) {
          return response(Checklist::where("trip_id", $id)->get(), 200);
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
  }

  public function tripDays($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $trip = Trips::find($id);

      if ($user_id == $trip->user_id) {
        if (Trips::where("id", $id)->exists()) {
          return response(Day::where("trip_id", $id)->get(), 200);
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
  }

  public function tripFirstDiary($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $trip = Trips::find($id);

      if ($user_id == $trip->user_id) {
        if (Trips::where("id", $id)->exists()) {
          if (Diary::where("trip_id", $id)->exists()) {
            return response(Diary::where("trip_id", $id)->get()[0], 200);
          } else {
            return response()->json(["message" => "Diary not found"], 404);
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
  }

  public function tripDiaries($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $trip = Trips::find($id);

      if ($user_id == $trip->user_id) {
        if (Trips::where("id", $id)->exists()) {
          if (Diary::where("trip_id", $id)->exists()) {
            return response(Diary::where("trip_id", $id)->get(), 200);
          } else {
            return response()->json(["message" => "Diary not found"], 404);
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
  }
}
