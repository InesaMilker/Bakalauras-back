<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiariesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create("diaries", function (Blueprint $table) {
      $table->id();
      $table->string("title");
      $table->text("content");
      $table->date("date");
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
    Schema::dropIfExists("diaries");
  }
}
