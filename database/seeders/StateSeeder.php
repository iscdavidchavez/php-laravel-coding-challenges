<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        State::factory(10)->create();

        State::factory()->create([
            'id' => 11,
            'name' => 'State1'
        ]);
        State::factory()->create([
            'id' => 12,
            'name' => 'State2'
        ]);
        State::factory()->create([
            'id' => 13,
            'name' => 'State3'
        ]);
        State::factory()->create([
            'id' => 14,
            'name' => 'State4'
        ]);
    }
}
