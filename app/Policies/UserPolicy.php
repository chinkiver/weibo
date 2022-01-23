<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct ()
    {
        //
    }

    /**
     * 当前登录用户ID与本身用户ID相同时，方可更新
     *
     * @param User $currentUser
     * @param User $user
     *
     * @return bool
     */
    public function update (User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }
}
