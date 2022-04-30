<?php

namespace Database\Factories;

use App\Models\Trips;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripsFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Trips::class;
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      "title" => $this->faker->name(),
      "start_date" => $this->faker->date(),
      "end_date" => $this->faker->date(),
      "rating" => "0.0",
      "place_id" => $this->faker->name(),
    ];
  }
}
