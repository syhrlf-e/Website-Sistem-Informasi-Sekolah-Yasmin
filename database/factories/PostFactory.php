<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'=> $this->faker->sentence(5, 10),
            'category_id'=> $this->faker->numberBetween(1, 2),
            'slug' => $this->faker->slug(5, 10),
            'excerpt' => $this->faker->sentence(10, 15),
            'body' => collect($this->faker->paragraphs(mt_rand(5, 10)))->map(fn($p)=> "<p>$p</p>")->implode('')
        ];
    }
}
