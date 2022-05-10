<?php

use App\Http\Controllers\LinksApiController;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RandomStringTest extends TestCase
{
  use DatabaseMigrations;

  public function test_random_string_generator()
  {
    $linkController = new LinksApiController();
    $randomString = $linkController->generateRandomString();

    $this->assertNotNull($randomString);

    $stringLength = strlen($randomString);
    $this->assertEquals($stringLength, 25);
  }
}
