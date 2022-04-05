<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clothes extends Model
{
  use \Illuminate\Database\Eloquent\Factories\HasFactory;

  protected $guarded = ["id"];

  public function outfits()
  {
    return $this->belongsToMany(Outfit::class, "clothes_outfits");
  }
}
