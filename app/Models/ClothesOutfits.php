<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothesOutfits extends Model
{
  use HasFactory;
  protected $fillable = ["clothes_id", "outfits_id"];
}
