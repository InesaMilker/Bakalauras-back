<?php

namespace Database\Factories;

use App\Models\Coordinates;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoordinatesFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Coordinates::class;
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      "location_name" => $this->faker->name(),
      "place_id" => $this->faker->name(),
      "lat" => $this->faker->numerify("##########"),
      "lng" => $this->faker->numerify("##########"),
    ];
  }
}
