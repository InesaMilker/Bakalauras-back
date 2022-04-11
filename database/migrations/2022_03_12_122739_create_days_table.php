<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaysTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create("days", function (Blueprint $table) {
      $table->id();
      $table->string("day_number");
      $table->foreignId("user_id");
      $table
        ->foreign("user_id")
        ->references("id")
        ->on("users")
        ->onDelete("cascade");
      $table->foreignId("trip_id");
      $table
        ->foreign("trip_id")
        ->references("id")
        ->on("trips")
        ->onDelete("cascade");
      $table->float("budget");
      $table->text("note");
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
    Schema::dropIfExists("days");
  }
}
