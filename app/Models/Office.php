<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// Temporarily comment out activity log imports
// use Spatie\Activitylog\Traits\LogsActivity;
// use Spatie\Activitylog\LogOptions;

class Office extends Model
{
    use HasFactory; // Temporarily remove LogsActivity

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'abbreviation',
        'parent_id'
    ];

    // Temporarily commented out activity log options
    /*
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name']); // Simplified logging - just log the name attribute
    }
    */

    /**
     * Get the personnel associated with this office.
     */
    public function personnel()
    {
        return $this->hasMany(Personnel::class, 'office', 'code');
    }

    /**
     * Get the parent office that owns this sub-office.
     */
    public function parentOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'parent_id');
    }

    /**
     * Get the sub-offices for this office.
     */
    public function subOffices(): HasMany
    {
        return $this->hasMany(Office::class, 'parent_id');
    }

    /**
     * Check if this office is a sub-office
     */
    public function isSubOffice(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if this office has sub-offices
     */
    public function hasSubOffices(): bool
    {
        return $this->subOffices()->count() > 0;
    }
}
