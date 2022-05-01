<?php

use App\Models\Checklist;
use App\Models\Trips;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ChecklistTest extends TestCase
{
  use DatabaseMigrations;

  /**
   * @var string
   */
  private $resource = "/api/checklist";

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

  public function test_get_all()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create();

    $checklist = Checklist::factory()
      ->count(10)
      ->for($this->user, "user")
      ->for($trips, "trips")
      ->create();

    $response = $this->get($this->resource);

    $response->assertStatus(200);

    $response->assertJson($checklist->toArray());
  }

  public function test_create()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create();

    $payload = [
      "text" => "Baseball cap",
    ];

    $response = $this->post("api/trips/$trips->id/checklist", $payload);

    $response->assertStatus(201);
    $response->assertJsonFragment($payload);
    $this->assertDatabaseHas((new Checklist())->getTable(), [
      "text" => $payload["text"],
      "trip_id" => $trips->id,
      "user_id" => $this->user->id,
    ]);
    $this->assertDatabaseHas((new Checklist())->getTable(), $payload);
  }

  public function test_get_single()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create();

    $checklist = Checklist::factory()
      ->for($this->user, "user")
      ->for($trips, "trips")
      ->create();

    $response = $this->get("$this->resource/$checklist->id");

    $response->assertStatus(200);

    $response->assertJson($checklist->toArray());
  }

  public function test_delete()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create();

    $checklist = Checklist::factory()
      ->for($this->user, "user")
      ->for($trips, "trips")
      ->create();

    $response = $this->delete("$this->resource/$checklist->id");

    $response->assertStatus(202);
  }
}
