<?php

namespace App\Http\Controllers;

use App\Models\Trips;

use Illuminate\Http\Request;

class TripsApiController extends Controller
{
    public function index() 
    {
        return Trips::all();
    }
}
