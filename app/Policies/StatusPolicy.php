<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    /**
     * 只能删除自己创建的微博
     *
     * @param User   $user
     * @param Status $status
     *
     * @return bool
     */
    public function destroy(User $user, Status $status)
    {
        return $user->id === $status->user_id;
    }
}
