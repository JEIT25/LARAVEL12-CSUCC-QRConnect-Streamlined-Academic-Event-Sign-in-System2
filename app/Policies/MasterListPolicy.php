<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\MasterList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MasterListPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->type == "facilitator";
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MasterList $master_list): bool
    {
        return $user->type == "facilitator" && $master_list->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,Event $event): bool
    {
        return $user->type == "facilitator" && $event->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MasterList $masterList): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MasterList $masterList): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MasterList $masterList): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MasterList $masterList): bool
    {
        return true;
    }
}
