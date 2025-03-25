<?php

namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Passwords>
 */
class PasswordsFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'site_name' => $this->faker->company,
            'password' => $this->faker->password,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
