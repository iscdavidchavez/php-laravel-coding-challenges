<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\StateMachine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use StateMachine, HasFactory;
    public static array $states = [
        'draft' => ['submitted'],
        'submitted' => ['approved', 'rejected'],
        'approved' => []
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    private function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->user()->where('role', 'patient');
    }

}
