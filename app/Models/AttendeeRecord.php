<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendeeRecord extends Model
{
    use HasFactory;
    protected $primaryKey = 'attendee_record_id';  // Update this line to use user_id instead of id

    protected $fillable = [
        'attendee_record_id',
        'master_list_member_id',
        'event_id',
        'check_in',
        'check_out',
        'single_signin'
    ];
    public function master_list_member()
    {
        return $this->belongsTo(MasterListMember::class, 'master_list_member_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

}
