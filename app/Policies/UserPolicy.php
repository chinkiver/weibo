<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * 当前登录用户ID与本身用户ID相同时，方可更新
     *
     * @param User $currentUser
     * @param User $user
     *
     * @return bool
     */
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

    /**
     * 当前用户为管理员，且自己不能删除自己
     *
     * @param User $currentUser
     * @param User $user
     *
     * @return bool
     */
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }

    /**
     * 用户不能自己关注自己
     *
     * @param User $currentUser
     * @param User $user
     *
     * @return bool
     */
    public function follow(User $currentUser, User $user)
    {
        return $currentUser->id !== $user->id;
    }
}
