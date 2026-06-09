<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * ClientFactory
 *
 * Factory sederhana untuk generate data client.
 * Client diperlukan sebagai foreign key saat membuat Project.
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'name'             => $this->faker->company(),
            'nik'              => $this->faker->unique()->numerify('##########'),
            'kode_organisasi'  => $this->faker->bothify('ORG-####'),
            'phone'            => $this->faker->phoneNumber(),
        ];
    }
}
