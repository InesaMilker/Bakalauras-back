<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use App\Models\Images;
use Illuminate\Http\Request;
use App\Models\Trips;
use Illuminate\Support\Facades\Validator;

class DiaryApiController extends Controller
{
  public function store()
  {
    $validator = Validator::make(request()->all(), [
      "title" => "required",
      "content" => "required",
      "date" => "required",
      "trip_id" => "required",
    ]);

    if ($validator->fails()) {
      return $validator->errors();
    }

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

      if (Diary::where("id", $id)->exists()) {
        $diary = Diary::find($id);

        if ($user_id == $diary->user_id) {
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

      if (Diary::where("id", $id)->exists()) {
        $diary = Diary::find($id);

        if ($user_id == $diary->user_id) {
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

  public function tripDiariesSingle($trip_id, $diary_id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      if (Trips::where("id", $trip_id)->exists()) {
        $trip = Trips::find($trip_id);

        if (Diary::where("id", $diary_id)->exists()) {
          $diary = Diary::find($diary_id);

          if ($user_id == $trip->user_id) {
            if ($user_id == $diary->user_id) {
              return Diary::find($diary_id);
            } else {
              return response()->json(["message" => "Unauthorized"], 401);
            }
          } else {
            return response()->json(["message" => "Unauthorized"], 401);
          }
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
  }

  public function diaryImages($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      if (Diary::where("id", $id)->exists()) {
        $diary = Diary::find($id);

        if ($user_id == $diary->user_id) {
          if (Images::where("diary_id", $id)->exists()) {
            $images = Images::where("diary_id", $id)->get();
            foreach ($images as $image) {
              $name = $image->name;
              $image_id = $image->id;
              $data[] = [
                "original" => "http://127.0.0.1:8000/uploads/$name",
                "thumbnail" => "http://127.0.0.1:8000/uploads/$name",
                "name" => $name,
                "id" => $image_id,
              ];
            }
            return response($data, 200);
          } else {
            return response()->json(["message" => "Image not found"], 404);
          }
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
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
  }
}
