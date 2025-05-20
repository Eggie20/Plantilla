<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;  // Import LogOptions

class Personnel extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAttributes = [
        'office_id',
        'itemNo',
        'position',
        'salaryGrade',
        'authorizedSalary',
        'actualSalary',
        'step',
        'code',
        'type',
        'level',
        'lastName',
        'firstName',
        'middleName',
        'dob',
        'originalAppointment',
        'lastPromotion',
        'status',
        'pendingRetirement',
        'retirement_date'
    ];
    
    protected $guarded = [];

    protected $dates = [
        'dob',
        'originalAppointment',
        'lastPromotion',
        'retirement_date'
    ];

    protected $fillable = [
        'office',
        'itemNo',
        'position',
        'salaryGrade',
        'authorizedSalary',
        'actualSalary',
        'step',
        'code',
        'type',
        'level',
        'lastName',
        'firstName',
        'middleName',
        'dob',
        'originalAppointment',
        'lastPromotion',
        'status',
        'pendingRetirement',
        'retirement_date'
    ];

    protected $casts = [
        'dob' => 'date',
        'originalAppointment' => 'date',
        'lastPromotion' => 'date'
    ];

    public static $rules = [
        // Position details (read-only from position)
        'position_id' => 'required|exists:positions,id',
        'itemNo' => 'required|string',
        'position' => 'required|string',
        'salaryGrade' => 'required|numeric',
        'step' => 'required|numeric',
        'code' => 'required|string',
        'type' => 'required|string',
        'level' => 'required|string',
        
        // Personnel details
        'lastName' => 'required|string',
        'firstName' => 'required|string',
        'middleName' => 'nullable|string',
        'dob' => 'nullable|date',
        'authorizedSalary' => 'required|numeric',
        'actualSalary' => 'required|numeric',
        'originalAppointment' => 'required|date',
        'lastPromotion' => 'nullable|date',
        'status' => 'required|string'
    ];

    public static $messages = [
        'position_id.required' => 'Please select a valid position to assign personnel to.',
        'position_id.exists' => 'Please select a valid position from the list.',
        'lastName.required' => 'Please enter a valid last name.',
        'firstName.required' => 'Please enter a valid first name.',
        'authorizedSalary.required' => 'Please enter a valid authorized salary.',
        'authorizedSalary.numeric' => 'Authorized salary must be a number.',
        'actualSalary.required' => 'Please enter a valid actual salary.',
        'actualSalary.numeric' => 'Actual salary must be a number.',
        'originalAppointment.required' => 'Please enter a valid original appointment date.',
        'originalAppointment.date' => 'Original appointment date must be in YYYY-MM-DD format.',
        'status.required' => 'Please select a valid status for the personnel.',
        'dob.date' => 'Date of birth must be in YYYY-MM-DD format.',
        'lastPromotion.date' => 'Last promotion date must be in YYYY-MM-DD format.'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Get the options for the activity log.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'office_id',
                'itemNo',
                'position',
                'salaryGrade',
                'authorizedSalary',
                'actualSalary',
                'step',
                'code',
                'type',
                'level',
                'lastName',
                'firstName',
                'middleName',
                'dob',
                'originalAppointment',
                'lastPromotion',
                'status',
                'pendingRetirement',
                'retirement_date'
            ])
            ->logOnlyDirty() // Only log changed attributes
            ->setDescriptionForEvent(fn(string $eventName) => "Personnel has been {$eventName}")
            ->useLogName('personnel');
    }
}
