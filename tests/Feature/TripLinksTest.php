<?php

use App\Models\Diary;
use App\Models\Images;
use App\Models\TripLinks;
use App\Models\Trips;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TripLinksTest extends TestCase
{
  use DatabaseMigrations;
  /**
   * @var string
   */
  private $resource = "/api/tripLink";

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

  public function test_store()
  {
    $payload = [
      "trip_id" => "1",
    ];
    $response = $this->post($this->resource, $payload);
    $response->assertStatus(404);

    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create();

    $payload = [
      "trip_id" => $trips->id,
    ];

    $response = $this->post($this->resource, $payload);
    $response->assertStatus(201);
    $response->assertJsonFragment($payload);

    $this->assertDatabaseHas((new TripLinks())->getTable(), [
      "trip_id" => $trips->id,
      "user_id" => $this->user->id,
    ]);
    $this->assertDatabaseHas((new TripLinks())->getTable(), $payload);
  }

  public function test_trip_link()
  {
    $response = $this->get("$this->resource/1");
    $response->assertStatus(404);

    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    Diary::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create(["date" => "2021-01-03"]);

    $tripLink = TripLinks::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create();

    $response = $this->get("$this->resource/$tripLink->link_number");
    $response->assertStatus(200);
  }

  public function test_get_all_photos()
  {
    $response = $this->get("$this->resource/1/images");
    $response->assertStatus(404);

    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $diary = Diary::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create(["date" => "2021-01-03"]);

    Images::factory()
      ->count(10)
      ->for($this->user, "user")
      ->for($diary, "diary")
      ->for($trip, "trips")
      ->create();

    $link = TripLinks::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create();

    $response = $this->get("$this->resource/$link->link_number/images");
    $response->assertStatus(200);
  }
}
