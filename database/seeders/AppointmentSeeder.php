<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Appointment::factory(10)->create();

        Appointment::factory()->create([
            'status' => 'draft',
            'location_id' => 11,
            'user_id' => 11,
            'created_at' => '2025-07-06 20:00:43.000'
        ]);
        Appointment::factory()->create([
            'status' => 'draft',
            'location_id' => 12,
            'user_id' => 12
        ]);
        Appointment::factory()->create([
            'status' => 'submitted',
            'location_id' => 12,
            'user_id' => 12
        ]);
        Appointment::factory()->create([
            'status' => 'approved',
            'location_id' => 13,
            'user_id' => 13
        ]);
        Appointment::factory()->create([
            'status' => 'rejected',
            'location_id' => 14,
            'user_id' => 14
        ]);
    }
}
