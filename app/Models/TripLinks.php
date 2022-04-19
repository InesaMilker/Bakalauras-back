<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripLinks extends Model
{
  use HasFactory;
  protected $fillable = ["link_number", "trip_id", "user_id"];
}
