<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
  use HasFactory;
  protected $fillable = ["state", "text", "trip_id", "user_id"];

  public function user()
  {
    return $this->belongsTo(User::class, "user_id", "id");
  }

  public function trips()
  {
    return $this->belongsTo(Trips::class, "trip_id", "id");
  }
}
