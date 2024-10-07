<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Builder;

class MasterList extends Model
{
    use HasFactory;
    protected $primaryKey = 'master_list_id';  // Update this line to use user_id instead of id

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'facilitator_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function master_list_members(): HasMany
    {
        return $this->hasMany(MasterListMember::class, "master_list_id");
    }


    protected $fillable = [
        'name',
        'facilitator_id',
        'event_id'
    ];
}
