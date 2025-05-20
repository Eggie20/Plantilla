<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\AuthLog;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        AuthLog::create([
            'user_id' => null, // No user ID because login failed
            'event' => 'failed',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
