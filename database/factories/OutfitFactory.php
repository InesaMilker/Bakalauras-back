<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OutfitFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      "outfit_name" => $this->faker->name(),
      "outfit_image" => $this->faker->image(),
    ];
  }
}
