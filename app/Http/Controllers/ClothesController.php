<?php

namespace App\Http\Controllers;
use App\Models\Outfit;
use App\Models\Clothes;

use Illuminate\Http\Request;

class ClothesController extends Controller
{
  public function all()
  {
    return Clothes::all();
  }

  public function create(Request $request)
  {
    // TODO: add validations

    $clothes = new Clothes();
    $clothes->state = $request->get("state");
    $clothes->text = $request->get("text");

    $clothes->save();

    return $clothes;
  }
}
