<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripLinksTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create("trip_links", function (Blueprint $table) {
      $table->id();
      $table->string("link_number");
      $table->foreignId("trip_id");
      $table
        ->foreign("trip_id")
        ->references("id")
        ->on("trips")
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
    Schema::dropIfExists("trip_links");
  }
}
