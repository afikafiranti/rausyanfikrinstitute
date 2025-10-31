<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'image_url' => 'https://picsum.photos/seed/' . rand(100,999) . '/800/600',
            'caption' => $this->faker->sentence(8),
        ];
    }
}
