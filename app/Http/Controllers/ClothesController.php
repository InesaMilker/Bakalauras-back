<?php

namespace App\Http\Controllers;
use App\Models\Outfit;
use App\Models\Clothes;

use Illuminate\Http\Request;

class ClothesController extends Controller
{
  public function create(Request $request)
  {
    $clothes = new Clothes();
    $clothes->state = 0;
    $clothes->text = "yes";

    $clothes->save();

    return "Success";
  }
}
