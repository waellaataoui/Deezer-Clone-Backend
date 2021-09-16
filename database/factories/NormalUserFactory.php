<?php

namespace Database\Factories;

use App\Models\Playlist;
use App\Models\NormalUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class NormalUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NormalUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (NormalUser $user) {
            //
        })->afterCreating(function (NormalUser $user) {
            $favouriteTracks = new Playlist;
            $favouriteTracks->fill(['name' => 'Favourite Tracks']);
            $favouriteTracks->owner()->associate($user);
            $user->favouriteTracks()->save($favouriteTracks);
        });
    }
}
