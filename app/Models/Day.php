<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
  use HasFactory;
  protected $fillable = ["day_number", "user_id", "trip_id", "budget", "note"];

  public function user()
  {
    return $this->belongsTo(User::class, "user_id", "id");
  }

  public function trips()
  {
    return $this->belongsTo(Trips::class, "trip_id", "id");
  }
}
