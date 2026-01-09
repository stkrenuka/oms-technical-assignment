<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
{
    return [
        'name'        => $this->faker->words(2, true),
        'price'       => $this->faker->numberBetween(100, 5000),
        'stock'       => $this->faker->numberBetween(0, 100),
        'status'      => $this->faker->randomElement(['active', 'inactive']),
        'description' => $this->faker->sentence(12),
        'image'       => 'products/images/ech_specs.jpeg',
    ];
}

}
