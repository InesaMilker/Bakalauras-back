<?php

use App\Models\Clothes;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ClothesTest extends TestCase
{
  use DatabaseMigrations;

  /**
   * @var string
   */
  private $resource = "/api/clothes";

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
    $clothes = Clothes::factory()
      ->count(10)
      ->for($this->user, "user")
      ->create();

    $response = $this->get($this->resource);

    $response->assertStatus(200);

    $response->assertJson($clothes->toArray());
  }

  public function test_delete()
  {
    $clothes = Clothes::factory()
      ->for($this->user, "user")
      ->create();

    $response = $this->delete("$this->resource/$clothes->id");

    $response->assertStatus(202);
  }

  public function test_create()
  {
    $payload = [
      "text" => "Baseball cap",
    ];

    $response = $this->post($this->resource, $payload);

    $response->assertStatus(201);
    $response->assertJsonFragment($payload);

    $this->assertDatabaseHas((new Clothes())->getTable(), $payload);
  }

  public function test_update()
  {
    $payload = [
      "state" => "1",
    ];

    $clothes = Clothes::factory()
      ->for($this->user, "user")
      ->create();

    $response = $this->put("$this->resource/$clothes->id", $payload);

    $response->assertStatus(200);
    $response->assertJsonFragment($payload);

    $this->assertDatabaseHas((new Clothes())->getTable(), $payload);
  }
}
