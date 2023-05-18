<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        return [
            'title' => $this->faker->text($this->faker->numberBetween(5, 255)),
            'content' => $this->faker->text($this->faker->numberBetween(5, 65535)),
            'user_id' => $this->faker->word,
            'created_at' => $this->faker->date('Y-m-d H:i:s'),
            'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
