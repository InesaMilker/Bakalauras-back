<?php

use App\Models\Diary;
use App\Models\Images;
use App\Models\Links;
use App\Models\Trips;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LinksTest extends TestCase
{
  use DatabaseMigrations;
  /**
   * @var string
   */
  private $resource = "/api/link";

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
    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $diary = Diary::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create(["date" => "2021-01-03"]);

    $payload = [
      "diary_id" => $diary->id,
    ];

    $response = $this->post($this->resource, $payload);

    $response->assertStatus(201);

    $response->assertJsonFragment($payload);

    $this->assertDatabaseHas((new Links())->getTable(), [
      "diary_id" => $diary->id,
      "user_id" => $this->user->id,
    ]);
    $this->assertDatabaseHas((new Links())->getTable(), $payload);
  }

  public function test_get_link()
  {
    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $diary = Diary::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create(["date" => "2021-01-03"]);

    $link = Links::factory()
      ->for($this->user, "user")
      ->for($diary, "diary")
      ->create();

    $response = $this->get("$this->resource/$link->link_number");

    $response->assertStatus(200);
  }

  public function test_get_all_photos()
  {
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

    $link = Links::factory()
      ->for($this->user, "user")
      ->for($diary, "diary")
      ->create();

    $response = $this->get("$this->resource/$link->link_number/images");

    $response->assertStatus(200);
  }
}
