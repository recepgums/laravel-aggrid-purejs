<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory {
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition() {
		return [
			'message' => $this->faker->sentence,
			'user_id' => User::inRandomOrder()->first(),
			'post_id' => Post::inRandomOrder()->first(),
		];
	}
}
