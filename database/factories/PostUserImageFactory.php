<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostUserImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'image' => $this->faker->sentence(1, true) . 'png',
        ];
    }
}
