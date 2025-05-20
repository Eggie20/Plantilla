<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    public static function log($logName, $description, $subject, $properties = [])
    {
        try {
            // Get user information - ensure we always get the authenticated user
            $user = Auth::user();
            
            // If no user is authenticated, throw an exception
            if (!$user) {
                throw new \Exception('No authenticated user found for activity logging');
            }
            
            // Handle subject type and ID
            $subjectType = $subject ? (
                is_object($subject) && get_class($subject) === 'App\Models\Personnel' ? 'personnels' :
                (is_object($subject) ? get_class($subject) : 'System')
            ) : 'System';
            $subjectId = $subject ? (property_exists($subject, 'id') ? $subject->id : null) : null;
            
            // Prepare log entry
            $logEntry = [
                'log_name' => $logName,
                'description' => $description,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'causer_type' => get_class($user),
                'causer_id' => $user->id,
                'user_name' => $user->name,
                'properties' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert into activity log
            DB::table('activity_log')->insert($logEntry);

            // Log success for debugging
            Log::info("Activity log entry created for {$logName}: {$description}", [
                'log_entry' => $logEntry
            ]);

        } catch (\Exception $e) {
            // Log error with detailed information
            Log::error('Activity log insertion failed: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'log_name' => $logName,
                'description' => $description
            ]);

            throw $e;
        }
    }
}