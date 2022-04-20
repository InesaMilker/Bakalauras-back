<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create("images", function (Blueprint $table) {
      $table->id();
      $table->foreignId("diary_id");
      $table
        ->foreign("diary_id")
        ->references("id")
        ->on("diaries")
        ->onDelete("cascade");
      $table->foreignId("trip_id");
      $table
        ->foreign("trip_id")
        ->references("id")
        ->on("trips")
        ->onDelete("cascade");
      $table->mediumText("name")->nullable();
      $table->foreignId("user_id");
      $table
        ->foreign("user_id")
        ->references("id")
        ->on("users")
        ->onDelete("cascade");
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists("images");
  }
}
