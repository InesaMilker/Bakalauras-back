<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClothesFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      "state" => 0,
      "text" => $this->faker->name(),
    ];
  }
}
