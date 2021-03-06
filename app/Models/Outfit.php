<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outfit extends Model
{
  use HasFactory;

  protected $fillable = ["outfit_name", "outfit_image", "user_id"];

  public function clothes()
  {
    return $this->belongsToMany(Clothes::class, "clothes_outfits");
  }

  public function user()
  {
    return $this->belongsTo(User::class, "user_id", "id");
  }
}
