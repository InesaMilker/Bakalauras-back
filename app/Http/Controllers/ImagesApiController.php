<?php

namespace App\Http\Controllers;

use App\Models\Images;
use App\Models\Diary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImagesApiController extends Controller
{
  public function store(Request $request)
  {
    request()->validate([
      "name" => "required|mimes:jpg,jpeg,png,gif |max:4096",
      "diary_id" => "required",
    ]);

    $isGuest = auth()->guest();
    if (!$isGuest) {
      $user_id = auth()->user()->id;

      if (Diary::where("id", request("diary_id"))->exists()) {
        if (
          Diary::where("id", request("diary_id"))->first()->user_id == $user_id
        ) {
          $name = $request->file("name");
          $filename =
            time() . rand(1, 3) . "." . $name->getClientOriginalName();
          $name->move(public_path("uploads/"), $filename);

          $trip_id = Diary::where("id", request("diary_id"))->value("trip_id");

          return Images::create([
            "diary_id" => request("diary_id"),
            "trip_id" => $trip_id,
            "name" => $filename,
            "user_id" => $user_id,
          ]);
        } else {
          return response()->json(["message" => "Diary not found"], 404);
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
      $image = Images::find($id);

      if (Images::where("id", $id)->exists()) {
        if ($user_id == $image->user_id) {
          $name = Images::where("id", $id)->value("name");
          $image_path = "uploads/$name";

          if (File::exists($image_path)) {
            File::delete($image_path);
          }

          $image->delete();

          return response()->json(["message" => "Image deleted"], 202);
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
      } else {
        return response()->json(["message" => "Image not found"], 404);
      }
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }
}
