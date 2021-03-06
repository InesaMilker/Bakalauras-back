<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChecklistApiController;
use App\Http\Controllers\CoordinatesApiController;
use App\Http\Controllers\DiaryApiController;
use App\Http\Controllers\OutfitsApiController;
use App\Http\Controllers\TripsApiController;
use App\Http\Controllers\DayApiController;
use App\Http\Controllers\ClothesApiController;
use App\Http\Controllers\LinksApiController;
use App\Http\Controllers\TripLinkApiController;
use App\Http\Controllers\ImagesApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
  return $request->user();
});

Route::group(
  [
    "middleware" => "api",
    "prefix" => "auth",
  ],
  function ($router) {
    Route::post("/login", [AuthController::class, "login"]);
    Route::post("/register", [AuthController::class, "register"]);
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::post("/refresh", [AuthController::class, "refresh"]);
    Route::get("/me", [AuthController::class, "me"]);
  }
);

Route::delete("/delete", [AuthController::class, "destroy"]);

Route::get("/outfits", [OutfitsApiController::class, "index"]);
Route::post("/outfits", [OutfitsApiController::class, "store"]);
Route::put("/outfits/{id}", [OutfitsApiController::class, "update"]);
Route::delete("/outfits/{id}", [OutfitsApiController::class, "destroy"]);
Route::get("/outfit/{id}", [OutfitsApiController::class, "wantedOutfit"]);
Route::delete("/outfit/{outfit_id}/clothes/{clothes_id}", [
  OutfitsApiController::class,
  "detachItem",
]);
Route::post("/outfit/{id}", [OutfitsApiController::class, "attachItem"]);
Route::get("/outfitClothes/{id}", [
  OutfitsApiController::class,
  "wantedOutfitClothes",
]);

Route::get("/diary", [DiaryApiController::class, "index"]);
Route::post("/diary", [DiaryApiController::class, "store"]);
Route::put("/diary/{id}", [DiaryApiController::class, "update"]);
Route::delete("/diary/{id}", [DiaryApiController::class, "destroy"]);
Route::get("/trips/{trip_id}/diaries/{diary_id}", [
  DiaryApiController::class,
  "tripDiariesSingle",
]);

Route::post("/link", [LinksApiController::class, "store"]);
Route::get("/link/{id}", [LinksApiController::class, "diaryLink"]);
Route::get("/link/{id}/images", [LinksApiController::class, "diaryPhotos"]);

Route::post("/tripLink", [TripLinkApiController::class, "store"]);
Route::get("/tripLink/{id}", [TripLinkApiController::class, "tripLink"]);
Route::get("/tripLink/{id}/images", [
  TripLinkApiController::class,
  "allPhotos",
]);

Route::post("/coordinates", [CoordinatesApiController::class, "store"]);
Route::put("/coordinates/{id}", [CoordinatesApiController::class, "update"]);
Route::delete("/coordinates/{id}", [
  CoordinatesApiController::class,
  "destroy",
]);
Route::get("/coordinates/{id}", [CoordinatesApiController::class, "wanted"]);

Route::get("/clothes", [ClothesApiController::class, "all"]);
Route::post("/clothes", [ClothesApiController::class, "create"]);
Route::put("/clothes/{id}", [ClothesApiController::class, "update"]);
Route::delete("/clothes/{id}", [ClothesApiController::class, "destroy"]);

Route::get("/checklist", [ChecklistApiController::class, "index"]);
Route::post("/trips/{id}/checklist", [ChecklistApiController::class, "store"]);
Route::put("/checklist/{id}", [ChecklistApiController::class, "update"]);
Route::delete("/checklist/{id}", [ChecklistApiController::class, "destroy"]);
Route::get("/checklist/{id}", [ChecklistApiController::class, "wanted"]);

Route::post("/image", [ImagesApiController::class, "store"]);
Route::get("/diary/{id}/images", [DiaryApiController::class, "diaryImages"]);
Route::delete("/image/{id}", [ImagesApiController::class, "destroy"]);

Route::get("/day", [DayApiController::class, "index"]);
Route::post("/day", [DayApiController::class, "store"]);
Route::put("/day/{id}", [DayApiController::class, "update"]);
Route::get("/day/{id}", [DayApiController::class, "wanted"]);
Route::get("/day/{id}/coordinates", [
  DayApiController::class,
  "dayCoordinates",
]);

Route::get("/trips", [TripsApiController::class, "index"]);
Route::post("/trips", [TripsApiController::class, "store"]);
Route::put("/trips/{id}", [TripsApiController::class, "update"]);
Route::delete("/trips/{id}", [TripsApiController::class, "destroy"]);
Route::get("/trips/{id}", [TripsApiController::class, "wanted"]);

Route::get("/trips/{id}/checklist", [
  TripsApiController::class,
  "tripChecklist",
]);
Route::get("/trips/{id}/days", [TripsApiController::class, "tripDays"]);
Route::get("/trips/{id}/diary", [TripsApiController::class, "tripFirstDiary"]);
Route::get("/trips/{id}/diaries", [TripsApiController::class, "tripDiaries"]);
