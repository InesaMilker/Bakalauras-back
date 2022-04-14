<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoordinatesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create("coordinates", function (Blueprint $table) {
      $table->id();
      $table->double("lat");
      $table->double("lng");
      $table->foreignId("day_id");
      $table
        ->foreign("day_id")
        ->references("id")
        ->on("days")
        ->onDelete("cascade");
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
    Schema::dropIfExists("coordinates");
  }
}
