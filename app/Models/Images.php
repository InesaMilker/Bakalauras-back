<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
  use HasFactory;
  protected $fillable = ["name", "user_id", "diary_id", "trip_id"];

  public function user()
  {
    return $this->belongsTo(User::class, "user_id", "id");
  }

  public function diary()
  {
    return $this->belongsTo(Diary::class, "diary_id", "id");
  }
  public function trips()
  {
    return $this->belongsTo(Trips::class, "trip_id", "id");
  }
}
