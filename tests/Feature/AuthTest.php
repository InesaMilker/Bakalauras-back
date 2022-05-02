<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthTest extends TestCase
{
  use DatabaseMigrations;

  public function test_login()
  {
    /** @var User $user */
    $user = User::factory()->create([
      "password" => bcrypt($password = "i-love-laravel"),
    ]);

    $response = $this->post("/api/auth/login", [
      "email" => $user->email,
      "password" => $password,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
      "access_token",
      "token_type",
      "expires_in",
    ]);
  }

  public function test_login_with_bad_credentials()
  {
    /** @var User $user */
    $user = User::factory()->create();

    $response = $this->post("/api/auth/login", [
      "email" => $user->email,
      "password" => "bad_password",
    ]);

    $response->assertStatus(401);
  }

  /**
   * @depends test_login
   */
  public function test_logout()
  {
    /** @var User $user */
    $user = User::factory()->create([
      "password" => bcrypt($password = "i-love-laravel"),
    ]);

    $response = $this->post("/api/auth/login", [
      "email" => $user->email,
      "password" => $password,
    ]);

    $response->assertStatus(200);

    $content = json_decode($response->getContent());

    $response = $this->post(
      "/api/auth/logout",
      [],
      ["Authorization" => "Bearer $content->access_token"]
    );

    $response->assertStatus(200);
  }

  /**
   * @depends test_login
   */
  public function test_refresh()
  {
    /** @var User $user */
    $user = User::factory()->create([
      "password" => bcrypt($password = "i-love-laravel"),
    ]);

    $response = $this->post("/api/auth/login", [
      "email" => $user->email,
      "password" => $password,
    ]);

    $response->assertStatus(200);

    $content = json_decode($response->getContent());

    $response = $this->post(
      "/api/auth/refresh",
      [],
      ["Authorization" => "Bearer $content->access_token"]
    );

    $response->assertStatus(200);

    $response->assertJsonStructure([
      "access_token",
      "token_type",
      "expires_in",
    ]);
  }

  public function test_me()
  {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get("/api/auth/me");
    $response->assertStatus(200);

    $content = json_decode($response->getContent());
    $this->assertEquals($user->id, $content->id);
  }

  public function test_register()
  {
    $payload = [
      "email" => "email@email.com",
      "password" =>
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
      "password_confirmation" =>
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    ];

    $response = $this->post("/api/auth/register", $payload);
    $response->assertStatus(400);

    $payload = [
      "name" => "name",
      "email" => "email@email.com",
      "password" =>
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
      "password_confirmation" =>
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    ];

    $response = $this->post("/api/auth/register", $payload);
    $response->assertStatus(201);
  }

  public function test_destroy()
  {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->delete("/api/delete");
    $response->assertStatus(200);
    $response->assertStatus(200);

    json_decode($response->getContent());
    // $this->assertEquals($user->id, $content->id);
  }
}
