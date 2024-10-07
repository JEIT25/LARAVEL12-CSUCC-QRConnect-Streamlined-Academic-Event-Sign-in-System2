<?php

namespace App\Policies;

use App\Models\AttendeeRecord;
use App\Models\Event;
use App\Models\User;
use Database\Factories\AttendeeRecordFactory;
use Illuminate\Auth\Access\Response;

class AttendeeRecordPolicy
{
  /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Event $event): bool
    {
        return $user->type == "facilitator" && $event->facilitator_id === $user->user_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttendeeRecord $attendee_record): bool
    {
        return $user->type == "facilitator";
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,Event $event): bool
    {
        return $user->type == "facilitator" && $event->facilitator_id === $user->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AttendeeRecord $attendee_record): bool
    {
        return $user->type == "facilitator";
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttendeeRecord $attendee_record): bool
    {
        return $user->type == "facilitator";
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AttendeeRecord $attendee_record): bool
    {
        return $user->type == "facilitator";
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AttendeeRecord $attendee_record,Event $event): bool
    {
        return $user->type == "facilitator" && $event->user_id === $user->id;
    }
}
