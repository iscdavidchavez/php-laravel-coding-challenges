<?php

namespace Tests\Feature;

use App\Events\ModelTransitioned;
use App\Events\ModelTransitioning;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StateMachineTest extends TestCase
{
    use RefreshDatabase;

    public function testTransitionTo(): void
    {
        $this->seed();
        $appointment = Appointment::factory()->create(['status' => 'draft']);
        $this->assertTrue($appointment->transitionTo('submitted'));
        $this->assertTrue($appointment->transitionTo('approved'));
        $this->assertFalse($appointment->transitionTo('draft'));
    }

    public function testEventDispatched()
    {
        Event::fake();

        $this->seed();
        $appointment = Appointment::factory()->create(['status' => 'draft']);

        $appointment->transitionTo('submitted');

        Event::assertDispatched(ModelTransitioning::class);
        Event::assertDispatched(ModelTransitioned::class);
    }
}
