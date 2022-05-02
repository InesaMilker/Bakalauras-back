<?php

use App\Models\Coordinates;
use App\Models\Day;
use App\Models\Trips;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class DayTest extends TestCase
{
  use DatabaseMigrations;

  /**
   * @var string
   */
  private $resource = "/api/day";

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

  public function test_wanted()
  {
    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $day = Day::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create();

    $response = $this->get("$this->resource/$day->id");

    $response->assertStatus(200);

    $response->assertJson($day->toArray());
  }

  public function test_get_all()
  {
    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $day = Day::factory()
      ->count(10)
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create();

    $response = $this->get("$this->resource");

    $response->assertStatus(200);

    $response->assertJson($day->toArray());
  }

  public function test_get_coordinates()
  {
    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $day = Day::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create();

    $coordinates = Coordinates::factory()
      ->count(10)
      ->for($this->user, "user")
      ->for($day, "day")
      ->create();
    $response = $this->get("$this->resource/$day->id/coordinates");

    $response->assertStatus(200);

    $response->assertJson($coordinates->toArray());
  }

  public function test_store()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $payload = [
      "day_number" => "1",
      "budget" => "200.2",
      "note" => "hello, this is my note",
      "trip_id" => $trips->id,
    ];

    $response = $this->post($this->resource, $payload);

    $response->assertStatus(201);
    $response->assertJsonFragment($payload);
    $this->assertDatabaseHas((new Day())->getTable(), [
      "day_number" => $payload["day_number"],
      "budget" => $payload["budget"],
      "note" => $payload["note"],
      "trip_id" => $trips->id,
      "user_id" => $this->user->id,
    ]);
    $this->assertDatabaseHas((new Day())->getTable(), $payload);
  }

  public function test_update()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $day = Day::factory()
      ->for($this->user, "user")
      ->for($trips, "trips")
      ->create();

    $payload = [
      "budget" => "200.2",
      "note" => "hello, this is my note",
    ];

    $response = $this->put("$this->resource/$day->id", $payload);

    $response->assertStatus(200);
    $this->assertDatabaseHas((new Day())->getTable(), $payload);
  }
}
