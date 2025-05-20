<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\AuthLog;
use App\Models\User;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Get the user ID
        $userId = $event->user->id;

        // Check if the user exists in the database
        $user = User::find($userId);
        
        // Only create auth log if user exists and is not a fresh registration
        if ($user && $user->exists) {
            AuthLog::create([
                'user_id' => $userId,
                'event' => 'login',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
