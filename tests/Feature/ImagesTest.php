<?php

use App\Models\Diary;
use App\Models\Images;
use App\Models\Trips;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImagesTest extends TestCase
{
  use DatabaseMigrations;
  /**
   * @var string
   */
  private $resource = "/api/image";

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
      "name" => UploadedFile::fake()->image("newName.jpg"),
      "diary_id" => $diary->id,
    ];

    $response = $this->post($this->resource, $payload);

    $response->assertStatus(201);

    $response->assertJsonFragment($payload);
  }

  public function test_delete()
  {
    $trip = Trips::factory()
      ->for($this->user, "user")
      ->create(["start_date" => "2020-01-03", "end_date" => "2023-01-03"]);

    $diary = Diary::factory()
      ->for($this->user, "user")
      ->for($trip, "trips")
      ->create(["date" => "2021-01-03"]);

    Images::factory()
      ->for($this->user, "user")
      ->for($diary, "diary")
      ->for($trip, "trips")
      ->create();

    $response = $this->delete("$this->resource/$diary->id");

    $response->assertStatus(202);
  }
}
