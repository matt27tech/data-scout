<?php

namespace Database\Factories;

use App\Models\TgUserMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TgUserMessageFactory extends Factory
{
    protected $model = TgUserMessage::class;

    public function definition(): array
    {
        return [
            'tg_user_id' => $this->faker->randomNumber(),
            'message_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
