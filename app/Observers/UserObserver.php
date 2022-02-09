<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user)
    {
        // 生成随机字符串
        $user->activation_token = Str::random(10);
    }
}
