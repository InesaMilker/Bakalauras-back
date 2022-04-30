<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinates extends Model
{
  use HasFactory;
  protected $fillable = [
    "location_name",
    "place_id",
    "lat",
    "lng",
    "day_id",
    "user_id",
  ];
  public function user()
  {
    return $this->belongsTo(User::class, "user_id", "id");
  }

  public function day()
  {
    return $this->belongsTo(Day::class, "day_id", "id");
  }
}
