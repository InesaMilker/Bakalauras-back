<?php

use App\Models\Diary;
use App\Models\Trips;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class DiaryTest extends TestCase
{
  use DatabaseMigrations;

  /**
   * @var string
   */
  private $resource = "/api/diary";

  /**
   * @var User
   */
  private $user;

  protected function setUp(): void
  {
    parent::setUp();

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
  }

  public function test_trip_diaries_single()
  {
    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $diary = Diary::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create(["date" => "2021-01-03"]);

    $response = $this->get("/api/trips/$trip->id/diaries/$diary->id");

    $response->assertStatus(200);

    $response->assertJson($diary->toArray());
  }

  public function test_store()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $payload = [
      "title" => "Baseball cap",
      "content" => "Baseball cap",
      "date" => "2021-11-12",
      "trip_id" => $trips->id,
    ];

    $response = $this->post($this->resource, $payload);

    $response->assertStatus(201);
    $response->assertJsonFragment($payload);
    $this->assertDatabaseHas((new Diary())->getTable(), [
      "title" => $payload["title"],
      "content" => $payload["content"],
      "date" => $payload["date"],
      "trip_id" => $trips->id,
      "user_id" => $this->user->id,
    ]);
    $this->assertDatabaseHas((new Diary())->getTable(), $payload);
  }
}
