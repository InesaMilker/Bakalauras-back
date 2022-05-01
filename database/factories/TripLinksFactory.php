<?php

namespace Database\Factories;

use App\Models\TripLinks;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripLinksFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = TripLinks::class;
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
