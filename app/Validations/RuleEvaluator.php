<?php

declare(strict_types=1);

namespace App\Validations;

use App\Models\User;
use App\Rules\ContainsSubstring;
use App\Rules\Equals;
use App\Rules\NotEquals;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\NotIn;

class RuleEvaluator
{
    public function canPerformAction(User $user, array $rules): bool
    {
        $validatorRules = [];

        foreach ($rules as $rule) {
            $validatorRules[$rule['field']] = [
                $this->getRule($rule['operator'], $rule['value'])
            ];
        }

        return !validator($user->toArray(), $validatorRules)->fails();
    }

    private function getRule(string $operator, mixed $value): ContainsSubstring|Equals|In|NotEquals|NotIn|string
    {
        return match ($operator) {
            '!=' => new NotEquals($value),
            'in' => Rule::in($value),
            'not_in' => Rule::notIn($value),
            '>' => "gt:$value",
            '<' => "lt:$value",
            'contains' => new ContainsSubstring($value),
            default => new Equals($value)
        };
    }
}
