<?php

namespace App\Http\Controllers;
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
      "text" => "required",
    ]);

    $isGuest = auth()->guest();
    $user_id = auth()->user()->id;

    if (!$isGuest) {
      $clothes = new Clothes();
      $clothes->text = $request->get("text");
      $clothes->user_id = $user_id;
      $clothes->save();

      return $clothes;
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function update(Request $request, $id)
  {
    $isGuest = auth()->guest();

    if ($isGuest) {
      return response()->json(["message" => "Unauthorized"], 401);
    }

    $clothes = Clothes::find($id);

    if ($clothes === null) {
      return response()->json(["message" => "Clothes item not found"], 404);
    }

    $user_id = auth()->user()->id;

    if ($user_id != $clothes->user_id) {
      return response()->json(["message" => "Clothes not found"], 404);
    }

    $clothes->state = is_null($request->state)
      ? $clothes->state
      : $request->state;

    $clothes->text = is_null($request->text) ? $clothes->text : $request->text;

    $clothes->user_id = $clothes->user_id;
    $clothes->save();

    return response()->json(
      ["message" => "Clothes item updated successfully", "clothes" => $clothes],
      200
    );
  }

  public function destroy($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;

      if (Clothes::where("id", $id)->exists()) {
        $clothes = Clothes::find($id);

        if ($user_id == $clothes->user_id) {
          $clothes->delete();

          return response()->json(["message" => "Clothes item deleted"], 202);
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
      } else {
        return response()->json(["message" => "clothes item not found"], 404);
      }
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }
}
