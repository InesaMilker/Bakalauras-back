<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diary extends Model
{
  use HasFactory;
  protected $fillable = ["title", "content", "date", "user_id", "trip_id"];

  public function user()
  {
    return $this->belongsTo(User::class, "user_id", "id");
  }

  public function trips()
  {
    return $this->belongsTo(Trips::class, "trip_id", "id");
  }
}
