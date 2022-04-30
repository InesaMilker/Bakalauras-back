<?php

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
