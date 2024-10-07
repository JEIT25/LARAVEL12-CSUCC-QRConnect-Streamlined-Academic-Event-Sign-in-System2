<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id';  // Update this line to use user_id instead of id

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'facilitator_id');
    }


    public function master_list(): HasOne
    {
        return $this->hasOne(MasterList::class, 'master_list_id');
    }

    public function attendee_records()
    {
        return $this->hasMany(AttendeeRecord::class, 'event_id');
    }




    protected $fillable = [
        'facilitator_id',
        'type',
        'subject',
        'name',
        'description',
        'profile_image',
        'start_date',
        'end_date',
        'location',
        'subject_code',
        'semester',
        'school_year',
    ];
}
