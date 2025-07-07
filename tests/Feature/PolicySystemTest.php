<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Validations\RuleEvaluator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicySystemTest extends TestCase
{
    use RefreshDatabase;

    private const string JSON_RULES = '{"action": "submit_form","rules": [{"field": "role", "operator": "==", "value": "staff"},{"field": "email_verified_at", "operator": "!=", "value": null}]}';

    public function testCanNotPerformAction(): void
    {
        $rules = json_decode(self::JSON_RULES, true);

        $validator = new RuleEvaluator();

        $user = User::factory()->create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => null,
            'role' => 'patient'
        ]);

        $this->assertFalse($validator->canPerformAction($user, $rules['rules']));
    }

    public function testCanPerformAction(): void
    {
        $rules = json_decode(self::JSON_RULES, true);

        $validator = new RuleEvaluator();

        $user = User::factory()->create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role' => 'staff'
        ]);

        $this->assertTrue($validator->canPerformAction($user, $rules['rules']));
    }

    public function testEachOperator(): void
    {
        $rules = [
            [[
                'field' => 'role',
                'operator' => '==',
                'value' => 'staff'
            ]],
            [[
                'field' => 'role',
                'operator' => '!=',
                'value' => 'patient'
            ]],
            [[
                'field' => 'role',
                'operator' => 'in',
                'value' => ['staff', 'patient']
            ]],
            [[
                'field' => 'role',
                'operator' => 'not_in',
                'value' => ['patient', 'doctor']
            ]],
            [[
                'field' => 'id',
                'operator' => '>',
                'value' => 0
            ]],
            [[
                'field' => 'id',
                'operator' => '<',
                'value' => 2
            ]],
            [[
                'field' => 'role',
                'operator' => 'contains',
                'value' => 'ff'
            ]]
        ];

        $user = User::factory()->create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => null,
            'role' => 'staff'
        ]);

        $validator = new RuleEvaluator();

        $this->assertTrue($validator->canPerformAction($user, $rules[0]));
        $this->assertTrue($validator->canPerformAction($user, $rules[1]));
        $this->assertTrue($validator->canPerformAction($user, $rules[2]));
        $this->assertTrue($validator->canPerformAction($user, $rules[3]));
        $this->assertTrue($validator->canPerformAction($user, $rules[4]));
        $this->assertTrue($validator->canPerformAction($user, $rules[5]));
        $this->assertTrue($validator->canPerformAction($user, $rules[6]));
    }

}
