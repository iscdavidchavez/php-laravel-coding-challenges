<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ModelTransitioned;
use App\Events\ModelTransitioning;
use Illuminate\Support\Facades\Log;

class ModelTransitionNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(ModelTransitioning|ModelTransitioned $event): void
    {
        Log::info('event dispatched');
    }
}
