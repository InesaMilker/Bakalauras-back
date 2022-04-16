<?php

namespace App\Http\Controllers;
use App\Models\Outfit;
use App\Models\Clothes;

use Illuminate\Http\Request;

class ClothesController extends Controller
{
  public function all()
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      return Clothes::where("user_id", $user_id)->get();
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function create(Request $request)
  {
    request()->validate([
      "state" => "required",
      "text" => "required",
    ]);

    $isGuest = auth()->guest();

    if (!$isGuest) {
      $clothes = new Clothes();
      $clothes->state = $request->get("state");
      $clothes->text = $request->get("text");

      $clothes->save();

      return $clothes;
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }
}
