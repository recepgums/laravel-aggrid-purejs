<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory {
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition() {
		return [
			'title' => $this->faker->sentence,
			'body' => $this->faker->paragraph,
			'user_id' => User::inRandomOrder()->first(),
		];
	}
}
