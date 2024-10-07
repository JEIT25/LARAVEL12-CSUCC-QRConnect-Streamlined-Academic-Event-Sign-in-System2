<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->type === 'admin'; // Allow only admins to view any users
    }

    public function view(User $user, User $model)
    {
        return $user->type === 'admin' || $user->id === $model->id; // Admins or users themselves can view
    }

    public function create(User $user)
    {
        return $user->type === 'admin'; // Only admins can create users
    }

    public function update(User $user, User $model)
    {
        return $user->type === 'admin'; // Only admins can update users
    }

    public function delete(User $user, User $model)
    {
        return $user->type === 'admin'; // Only admins can delete users
    }
}
