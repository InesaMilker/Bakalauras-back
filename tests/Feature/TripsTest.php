<?php

use App\Models\Checklist;
use App\Models\Day;
use App\Models\Diary;
use App\Models\Trips;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TripsTest extends TestCase
{
  use DatabaseMigrations;

  /**
   * @var string
   */
  private $resource = "/api/trips";

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
      ->count(1)
      ->for($this->user, "user")
      ->create();

    $response = $this->get($this->resource);

    $response->assertStatus(200);
    $response->assertJson($trips->toArray());
  }

  public function test_get_wanted()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create();

    $response = $this->get("$this->resource/$trips->id");

    $response->assertStatus(200);
    $response->assertJson($trips->toArray());
  }

  public function test_delete()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create();

    $response = $this->delete("$this->resource/$trips->id");

    $response->assertStatus(202);
  }

  public function test_get_diaries()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $diary = Diary::factory()
      ->count(3)
      ->for($this->user, "user")
      ->for($trips, "trips")
      ->create(["date" => "2021-01-03"]);

    $response = $this->get("$this->resource/$trips->id/diaries");
    $response->assertStatus(200);
    $response->assertJson($diary->toArray());
  }

  public function test_get_checklist()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create();

    $checklist = Checklist::factory()
      ->count(10)
      ->for($this->user, "user")
      ->for($trips, "trips")
      ->create();

    $response = $this->get("$this->resource/$trips->id/checklist");

    $response->assertStatus(200);
    $response->assertJson($checklist->toArray());
  }

  public function test_get_days()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create();

    $day = Day::factory()
      ->count(10)
      ->for($this->user, "user")
      ->for($trips, "trips")
      ->create();

    $response = $this->get("$this->resource/$trips->id/days");

    $response->assertStatus(200);
    $response->assertJson($day->toArray());
  }

  public function test_get_first_diary()
  {
    $trips = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $diary = Diary::factory()
      ->for($this->user, "user")
      ->for($trips, "trips")
      ->create(["date" => "2021-01-03"]);

    $response = $this->get("$this->resource/$trips->id/diary");

    $response->assertStatus(200);
    $response->assertJson($diary->toArray());
  }

  public function test_create()
  {
    $payload = [
      "title" => "John Doe",
      "start_date" => "2022-03-11",
      "end_date" => "2023-03-25",
      "place_id" => "Josh Doe",
    ];

    $response = $this->post($this->resource, $payload);

    $response->assertStatus(201);
    $response->assertJsonFragment($payload);

    $this->assertDatabaseHas((new Trips())->getTable(), $payload);
  }
}
