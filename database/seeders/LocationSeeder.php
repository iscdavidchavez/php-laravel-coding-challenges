<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::factory(10)->create();
        Location::factory()->create([
            'id' => 11,
            'name' => 'Location1',
            'state_id' => 11
        ]);
        Location::factory()->create([
            'id' => 12,
            'name' => 'Location2',
            'state_id' => 12
        ]);
        Location::factory()->create([
            'id' => 13,
            'name' => 'Location3',
            'state_id' => 13
        ]);
        Location::factory()->create([
            'id' => 14,
            'name' => 'Location4',
            'state_id' => 14
        ]);
    }
}
