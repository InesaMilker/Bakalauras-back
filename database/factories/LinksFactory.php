<?php

namespace Database\Factories;

use App\Models\Links;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinksFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Links::class;
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      "link_number" => $this->faker->randomNumber(),
    ];
  }
}
