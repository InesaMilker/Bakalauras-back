<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Clothes;
use App\Models\Outfit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class OutfitsApiController extends Controller
{
  public function index()
  {
    $isGuest = auth()->guest();

    if ($isGuest) {
      return response()->json(["message" => "Unauthorized"], 401);
    }

    $user_id = auth()->user()->id;

    return Outfit::where("user_id", $user_id)->get();
  }

  public function store(Request $request)
  {
    $myValues = $request->get("clothes");

    $results = Clothes::whereIn("id", $myValues)->count();

    if ($results !== count($myValues)) {
      return response()->json(
        ["message" => "Some clothes items do not exist"],
        404
      );
    }

    $validator = Validator::make(request()->all(), [
      "outfit_name" => "required",
      "outfit_image" => "required|mimes:jpg,jpeg,png,gif |max:4096",
      "clothes" => "required|array|min:2",
      "clothes.*" => "required|integer|exists:clothes,id",
    ]);

    if ($validator->fails()) {
      return $validator->errors();
    }

    if (auth()->guest()) {
      return response()->json(["message" => "Unauthorized"], 401);
    }

    if (Outfit::where("outfit_name", request("outfit_name"))->exists()) {
      return response()->json(
        ["message" => "Outfit with provided name already exist"],
        400
      );
    }

    $user_id = auth()->user()->id;

    $image = $request->file("outfit_image");
    $filename = time() . rand(1, 3) . "." . $image->getClientOriginalName();
    $image->move("uploads/", $filename);

    $outfit = Outfit::create([
      "outfit_name" => request("outfit_name"),
      "outfit_image" => $filename,
      "user_id" => $user_id,
    ]);

    $outfit->clothes()->attach($request->get("clothes"));

    return $outfit;
  }

  public function destroy($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;

      if (Outfit::where("id", $id)->exists()) {
        $outfit = Outfit::find($id);

        if ($user_id == $outfit->user_id) {
          $name = Outfit::where("id", $id)->value("outfit_image");
          $image_path = "uploads/$name";

          if (File::exists($image_path)) {
            File::delete($image_path);
          }

          $outfit->clothes()->detach();
          $outfit->delete();

          return response()->json(["message" => "Outfit deleted"], 202);
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
      } else {
        return response()->json(["message" => "Outfit not found"], 404);
      }
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function wantedOutfit($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      $outfit = Outfit::find($id);
      if (Outfit::where("id", $id)->exists()) {
        if ($user_id == $outfit->user_id) {
          return $outfit;
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
      } else {
        return response()->json(
          [
            "message" => "Outfit not found",
          ],
          404
        );
      }
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function wantedOutfitClothes($id)
  {
    $isGuest = auth()->guest();

    if (!$isGuest) {
      $user_id = auth()->user()->id;
      if (Outfit::where("id", $id)->exists()) {
        $outfit = Outfit::find($id);

        if ($user_id == $outfit->user_id) {
          $result = DB::table("clothes_outfits")
            ->where("outfit_id", $id)
            ->pluck("clothes_id");

          $clothes = Clothes::whereIn("id", $result)->get();
          return $clothes;
        } else {
          return response()->json(["message" => "Unauthorized"], 401);
        }
      } else {
        return response()->json(
          [
            "message" => "Outfit not found",
          ],
          404
        );
      }
    } else {
      return response()->json(["message" => "Unauthorized"], 401);
    }
  }

  public function detachItem($outfit_id, $clothes_id)
  {
    if (Outfit::where("id", $outfit_id)->exists()) {
      $outfit = Outfit::find($outfit_id);
      if (Clothes::where("id", $clothes_id)->exists()) {
        $outfit->clothes()->detach($clothes_id);
        return response()->json(["message" => "Clothes item detached"], 202);
      } else {
        return response()->json(
          [
            "message" => "Clothes item not found",
          ],
          404
        );
      }
    } else {
      return response()->json(
        [
          "message" => "Outfit not found",
        ],
        404
      );
    }
  }

  public function attachItem($outfit_id, Request $request)
  {
    if (Outfit::where("id", $outfit_id)->exists()) {
      $outfit = Outfit::find($outfit_id);
      $myValues = $request->get("clothes");

      $results = Clothes::whereIn("id", $myValues)->count();

      if ($results !== count($myValues)) {
        return response()->json(
          ["message" => "Some clothes items do not exist"],
          404
        );
      }

      $outfit->clothes()->attach($request->get("clothes"));

      return response()->json(["message" => "Clothes items attached"], 202);
    } else {
      return response()->json(
        [
          "message" => "Outfit not found",
        ],
        404
      );
    }
  }
}
