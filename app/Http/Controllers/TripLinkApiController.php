<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use App\Models\TripLinks;
use App\Models\Trips;

class TripLinkApiController extends Controller
{
  public function store()
  {
    request()->validate([
      "trip_id" => "required",
    ]);

    $isGuest = auth()->guest();
    $user_id = auth()->user()->id;

    if (!$isGuest) {
      if (Trips::where("id", request("trip_id"))->exists()) {
        if (
          Trips::where("id", request("trip_id"))->first()->user_id == $user_id
        ) {
          return TripLinks::create([
            "link_number" => $this->generateRandomString(),
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
    $length = 25;
    $characters =
      "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    if (TripLinks::where("link_number", $randomString)->exists()) {
      $this->generateRandomString();
    } else {
      return $randomString;
    }
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
}
