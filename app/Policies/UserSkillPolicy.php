<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserSkill;

class UserSkillPolicy
{
    public function update(User $user, UserSkill $userSkill): bool
    {
        return $user->role === 'student' && $userSkill->user_id === $user->id;
    }

    public function delete(User $user, UserSkill $userSkill): bool
    {
        return $this->update($user, $userSkill);
    }
}
