<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\AuthLog;
use App\Models\User;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if ($event->user) {
            $userId = $event->user->id;
            $user = User::find($userId);
            
            if ($user && $user->exists) {
                AuthLog::create([
                    'user_id' => $userId,
                    'event' => 'logout',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        }
    }
}
