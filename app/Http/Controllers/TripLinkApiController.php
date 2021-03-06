<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use App\Models\Images;
use App\Models\TripLinks;
use App\Models\Trips;
use Illuminate\Support\Facades\Validator;

class TripLinkApiController extends Controller
{
  public function store()
  {
    $validator = Validator::make(request()->all(), [
      "trip_id" => "required",
    ]);

    if ($validator->fails()) {
      return $validator->errors();
    }

    $isGuest = auth()->guest();
    $user_id = auth()->user()->id;

    if (!$isGuest) {
      if (Trips::where("id", request("trip_id"))->exists()) {
        if (
          Trips::where("id", request("trip_id"))->first()->user_id == $user_id
        ) {
          $new = false;

          while ($new == false) {
            $randomString = $this->generateRandomString();

            if (TripLinks::where("link_number", $randomString)->exists()) {
              $randomString = $this->generateRandomString();
            } else {
              $new = true;
            }
          }

          return TripLinks::create([
            "link_number" => $randomString,
            "trip_id" => request("trip_id"),
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

  public function generateRandomString()
  {
    $length = 35;
    $characters =
      "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  public function tripLink($id)
  {
    if (TripLinks::where("link_number", $id)->exists()) {
      $trip_id = TripLinks::where("link_number", $id)
        ->get()
        ->pluck("trip_id");

      if (Trips::where("id", $trip_id)->exists()) {
        if (Diary::where("trip_id", $trip_id)->exists()) {
          return response(Diary::where("trip_id", $trip_id)->get(), 200);
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
      return response()->json(
        [
          "message" => "Trip not found",
        ],
        404
      );
    }
  }

  public function allPhotos($id)
  {
    if (TripLinks::where("link_number", $id)->exists()) {
      $trip_id = TripLinks::where("link_number", $id)
        ->get()
        ->pluck("trip_id");

      if (Trips::where("id", $trip_id)->exists()) {
        if (Images::where("trip_id", $trip_id)->exists()) {
          $images = Images::where("trip_id", $trip_id)->get();
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
        return response()->json(
          [
            "message" => "Trip not found",
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
  }
}
