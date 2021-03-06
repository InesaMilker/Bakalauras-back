<?php

use App\Models\Clothes;
use App\Models\Outfit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class OutfitTest extends TestCase
{
  use DatabaseMigrations;

  /**
   * @var string
   */
  private $resource = "/api/outfits";

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
    $outfit = Outfit::factory()
      ->count(10)
      ->for($this->user, "user")
      ->has(
        Clothes::factory()
          ->count(2)
          ->for($this->user, "user")
      )
      ->create();

    $response = $this->get($this->resource);

    $response->assertStatus(200);

    $response->assertJson($outfit->toArray());
  }

  public function test_create()
  {
    $clothes = Clothes::factory()
      ->count(3)
      ->for($this->user, "user")
      ->create();

    $payload = [
      "outfit_name" => "Sexy nurse outfit",
      "outfit_image" => UploadedFile::fake()->image("nurse.jpg"),
      "clothes" => collect([24, 2, 3]),
    ];
    $response = $this->post($this->resource, $payload);
    $response->assertStatus(404);

    $payload = [
      "outfit_name" => "Sexy nurse outfit",
      "outfit_image" => UploadedFile::fake()->image("nurse.jpg"),
      "clothes" => $clothes->pluck("id")->toArray(),
    ];

    $response = $this->post($this->resource, $payload);

    $response->assertStatus(201);

    unset($payload["clothes"], $payload["outfit_image"]);

    $response->assertJsonFragment($payload);

    $this->assertDatabaseHas((new Outfit())->getTable(), $payload);

    $outfit = $response->baseResponse->original;

    foreach ($clothes as $singleClothes) {
      $this->assertDatabaseHas("clothes_outfits", [
        "clothes_id" => $singleClothes->id,
        "outfit_id" => $outfit->id,
      ]);
    }
  }

  public function test_delete()
  {
    $outfit = Outfit::factory()
      ->for($this->user, "user")
      ->has(
        Clothes::factory()
          ->count(2)
          ->for($this->user, "user")
      )
      ->create();

    $response = $this->delete("$this->resource/$outfit->id");

    $response->assertStatus(202);
  }
  public function test_get_wanted()
  {
    $response = $this->get("api/outfit/1");

    $response->assertStatus(404);

    $outfit = Outfit::factory()
      ->for($this->user, "user")
      ->has(
        Clothes::factory()
          ->count(2)
          ->for($this->user, "user")
      )
      ->create();

    $response = $this->get("api/outfit/$outfit->id");

    $response->assertStatus(200);

    $response->assertJson($outfit->toArray());
  }

  public function test_get_clothes()
  {
    $response = $this->get("api/outfitClothes/1");
    $response->assertStatus(404);

    $clothes = Clothes::factory()
      ->count(2)
      ->for($this->user, "user");

    $outfit = Outfit::factory()
      ->for($this->user, "user")
      ->has($clothes)
      ->create();

    $response = $this->get("api/outfitClothes/$outfit->id");

    $response->assertStatus(200);
  }
}
