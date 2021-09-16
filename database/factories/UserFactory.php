<?php

namespace Database\Factories;

use App\Models\NormalUser;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'admin', //replace this with app's name
            'email' => 'admin@email.com',
            'email_verified_at' => now(),
            'avatar' => 'https://res.cloudinary.com/ds3s4afxk/image/upload/v1631790579/images/logo_ffxed9.png',
            'password' => Hash::make('12345'),
            'model_id' =>  NormalUser::factory()->create(),
            'model_type' => 'App\Models\NormalUser'
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
