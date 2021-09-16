<?php

namespace Database\Factories;

use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TrackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Track::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => (string) Str::uuid()->getInteger(),
            'name' => $this->faker->name(),
            'genres' => ['Pop'],
            'source' => 'https://res.cloudinary.com/ds3s4afxk/video/upload/v1626520304/Right_Now_Na_Na_Na_q9cyxa.mp3',
            'source_id' => '1626520304'
        ];
    }
}
