<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'itemNo',
        'office_id',
        'position',
        'salaryGrade',
        'monthlySalary',
        'step',
        'code',
        'type',
        'level',
        'status'
    ];

    public function personnel()
    {
        return $this->hasOne(Personnel::class, 'item_no', 'itemNo');
    }

    /**
     * Get the office that owns this position
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}