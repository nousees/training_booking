<?php

namespace App\Policies;

use App\Models\TrainerProfile;
use App\Models\User;

class TrainerProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TrainerProfile $profile): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isTrainer() && !$user->trainerProfile;
    }

    public function update(User $user, TrainerProfile $profile): bool
    {
        if ($user->isOwner()) {
            return true;
        }

        return $user->isTrainer() && $user->id === $profile->user_id;
    }

    public function delete(User $user, TrainerProfile $profile): bool
    {
        return $user->isOwner();
    }
}

