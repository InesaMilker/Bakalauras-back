<?php

namespace App\Http\Controllers;

use App\Models\Links;
use App\Models\Diary;
use App\Models\Images;
use Illuminate\Support\Facades\Validator;

class LinksApiController extends Controller
{
  public function store()
  {
    $validator = Validator::make(request()->all(), [
      "diary_id" => "required",
    ]);
    if ($validator->fails()) {
      return $validator->errors();
    }

    $isGuest = auth()->guest();

    if (!$isGuest) {
      if (Diary::where("id", request("diary_id"))->exists()) {
        $user_id = auth()->user()->id;

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
    if (Links::where("link_number", $randomString)->exists()) {
      $this->generateRandomString();
    } else {
      return $randomString;
    }
  }

  public function diaryLink($id)
  {
    if (Links::where("link_number", $id)->exists()) {
      $diary_id = Links::where("link_number", $id)
        ->get()
        ->pluck("diary_id");
      return Diary::where("id", $diary_id)->get()[0];
    } else {
      return response()->json(
        [
          "message" => "Diary not found",
        ],
        404
      );
    }
  }

  public function diaryPhotos($id)
  {
    if (Links::where("link_number", $id)->exists()) {
      $diary_id = Links::where("link_number", $id)
        ->get()
        ->pluck("diary_id");

      if (Diary::where("id", $diary_id)->exists()) {
        if (Images::where("diary_id", $diary_id)->exists()) {
          $images = Images::where("diary_id", $diary_id)->get();
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
            "message" => "Diary not found",
          ],
          404
        );
      }
    } else {
      return response()->json(
        [
          "message" => "Diary not found",
        ],
        404
      );
    }
  }
}
