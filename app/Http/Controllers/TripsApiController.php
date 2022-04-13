<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Day;
use App\Models\Diary;
use App\Models\Trips;
use Illuminate\Http\Request;

class TripsApiController extends Controller
{
  public function index()
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      return Trips::where("user_id", $user_id)->get();
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
    ]);

    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;

      return Trips::create([
        "title" => request("title"),
        "start_date" => request("start_date"),
        "end_date" => request("end_date"),
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
        $user_role = auth()->user()->role;

        if (Trips::where("id", $id)->exists()) {
          $trips = Trips::find($id);

          if ($user_id == $trips->user_id || $user_role == 1) {
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
      $user_role = auth()->user()->role;

      if (Trips::where("id", $id)->exists()) {
        $trips = Trips::find($id);

        if ($user_id == $trips->user_id || $user_role == 1) {
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
      $user_role = auth()->user()->role;
      $trips = Trips::find($id);

      if ($user_id == $trips->user_id || $user_role == 1) {
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

  public function tripChecklist($id, Checklist $checklist)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $user_role = auth()->user()->role;
      $checklist = Checklist::find($id);

      if ($user_id == $checklist->user_id || $user_role == 1) {
        if (Trips::where("id", $id)->exists()) {
          return response(
            $checklist = Checklist::where("trip_id", $id)->get(),
            200
          );
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

  public function tripDays($id, Day $day)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $day = Day::find($id);
      if ($user_id == $day->user_id) {
        if (Trips::where("id", $id)->exists()) {
          return response($day = Day::where("trip_id", $id)->get(), 200);
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

  public function tripFirstDiary($id, Diary $diary)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $user_role = auth()->user()->role;
      $diary = Diary::find($id);

      if ($user_id == $diary->user_id || $user_role == 1) {
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

  public function tripDiaries($id, Diary $diary)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $user_role = auth()->user()->role;
      $diary = Diary::find($id);

      if ($user_id == $diary->user_id || $user_role == 1) {
        if (Trips::where("id", $id)->exists()) {
          return response($diary = Diary::where("trip_id", $id)->get(), 200);
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
