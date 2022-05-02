<?php

use App\Models\Day;
use App\Models\Trips;
use App\Models\Coordinates;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CoordinatesTest extends TestCase
{
  use DatabaseMigrations;

  /**
   * @var string
   */
  private $resource = "/api/coordinates";

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

    $coordinate = Coordinates::factory()
      ->for($this->user, "user")
      ->for($day, "day")
      ->create();

    $response = $this->get("$this->resource/$coordinate->id");

    $response->assertStatus(200);

    $response->assertJson($coordinate->toArray());
  }

  public function test_delete()
  {
    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $day = Day::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create();

    $coordinate = Coordinates::factory()
      ->for($this->user, "user")
      ->for($day, "day")
      ->create();

    $response = $this->delete("$this->resource/$coordinate->id");

    $response->assertStatus(202);
  }

  public function test_store()
  {
    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $day = Day::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create();

    $payload = [
      "location_name" => "name of location",
      "place_id" => "jkasdhksa45646",
      "lat" => "5056465.0",
      "lng" => "56946654.1",
      "day_id" => $day->id,
    ];

    $response = $this->post($this->resource, $payload);

    $response->assertStatus(201);
    $response->assertJsonFragment($payload);
    $this->assertDatabaseHas((new Coordinates())->getTable(), [
      "location_name" => $payload["location_name"],
      "place_id" => $payload["place_id"],
      "lat" => $payload["lat"],
      "lng" => $payload["lng"],
      "day_id" => $day->id,
      "user_id" => $this->user->id,
    ]);
    $this->assertDatabaseHas((new Coordinates())->getTable(), $payload);
  }

  public function test_update()
  {
    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $day = Day::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create();

    $coordinate = Coordinates::factory()
      ->for($this->user, "user")
      ->for($day, "day")
      ->create();

    $payload = [
      "location_name" => "name of location",
      "lat" => "5056465.0",
      "lng" => "56946654.1",
    ];

    $response = $this->put("$this->resource/$coordinate->id", $payload);

    $response->assertStatus(200);

    $this->assertDatabaseHas((new Coordinates())->getTable(), $payload);
  }
}
