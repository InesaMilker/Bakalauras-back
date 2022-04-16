<?php

namespace App\Http\Controllers;

use App\Models\Links;
use App\Models\Diary;

class LinksApiController extends Controller
{
  public function store()
  {
    request()->validate([
      "diary_id" => "required",
    ]);

    $isGuest = auth()->guest();
    $user_id = auth()->user()->id;

    if (!$isGuest) {
      if (Diary::where("id", request("diary_id"))->exists()) {
        if (
          Diary::where("id", request("diary_id"))->first()->user_id == $user_id
        ) {
          return Links::create([
            "link_number" => $this->generateRandomString(),
            "diary_id" => request("diary_id"),
            "user_id" => $user_id,
          ]);
        } else {
          return response()->json(["message" => "Day not found"], 404);
        }
      } else {
        return response()->json(["message" => "Day not found"], 404);
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
    return $randomString;
  }
}
