<?php
use App\Models\Trips;
use App\Models\Outfit;
use App\Models\Diary;
use App\Models\Checklist;

use App\Http\Controllers\TripsApiController;
use App\Http\Controllers\OutfitsApiController;
use App\Http\Controllers\ChecklistApiController;
use App\Http\Controllers\DiaryApiController;
use App\Http\Controllers\CoordinatesApiController;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::get('/outfits', [OutfitsApiController::class, 'index']);

Route::post('/outfits', [OutfitsApiController::class, 'store']);

Route::put('/outfits/{id}', [OutfitsApiController::class, 'update']);

Route::delete('/outfits/{id}', [OutfitsApiController::class, 'destroy']);

Route::get('/outfits/{id}', [OutfitsApiController::class, 'wanted']);


Route::get('/diary', [DiaryApiController::class, 'index']);

Route::post('/diary', [DiaryApiController::class, 'store']);

Route::put('/diary/{id}', [DiaryApiController::class, 'update']);

Route::delete('/diary/{id}', [DiaryApiController::class, 'destroy']);

Route::get('/diary/{id}', [DiaryApiController::class, 'wanted']);


Route::get('/coordinates', [CoordinatesApiController::class, 'index']);


Route::get('/checklist', [ChecklistApiController::class, 'index']);

Route::post('/checklist', [ChecklistApiController::class, 'store']);

Route::put('/checklist/{id}', [ChecklistApiController::class, 'update']);

Route::delete('/checklist/{id}', [ChecklistApiController::class, 'destroy']);

Route::get('/checklist/{id}', [ChecklistApiController::class, 'wanted']);



Route::get('/trips', [TripsApiController::class, 'index']);