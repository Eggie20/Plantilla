<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Record user visit
        if (auth()->check()) {
            DB::table('activity_log')->insert([
                'log_name' => 'system',
                'description' => 'Visited',
                'causer_id' => auth()->id(),
                'causer_type' => 'App\Models\PlantillaUser',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $activities = DB::table('activity_log')
            ->select(
                'activity_log.log_name',
                'activity_log.description',
                'activity_log.subject_type',
                'activity_log.subject_id',
                'activity_log.causer_type',
                'activity_log.causer_id',
                'activity_log.user_name',
                'activity_log.created_at',
                'activity_log.updated_at',
                'plantilla_users.role'
            )
            ->leftJoin('plantilla_users', 'activity_log.causer_id', '=', 'plantilla_users.id')
            ->orderBy('activity_log.created_at', 'desc')
            ->paginate(20);

        // Format the dates for display
        $activities->getCollection()->transform(function($activity) {
            // Get the Carbon instance
            $time = Carbon::parse($activity->created_at);
            
            // Format for display
            $activity->formatted_time = $time->format('H:i:s');
            $activity->formatted_date = $time->format('Y-m-d');
            
            // Store the full datetime for sorting
            $activity->full_datetime = $time->format('Y-m-d H:i:s');
            
            return $activity;
        });

        return view('Plantilla.Pages.activity-log', compact('activities'));
    }
}