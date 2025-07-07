<?php

declare(strict_types=1);

namespace App\Traits;

use App\Events\ModelTransitioned;
use App\Events\ModelTransitioning;

trait StateMachine
{
    public function transitionTo(string $newState): bool
    {
        ModelTransitioning::dispatch();

        //! We assume that the column name is 'status'
        if (!$this->canTransition($this->status, $newState)) {
            return false;
        }

        $this->setAttribute('status', $newState);
        if (!$this->save()) {
            return false;
        }

        ModelTransitioned::dispatch();

        return true;
    }

    private function canTransition(string $oldState, string $newState): bool
    {
        $allowedStates = static::$states[$oldState] ?? [];

        return in_array($newState, $allowedStates, true);
    }
}
