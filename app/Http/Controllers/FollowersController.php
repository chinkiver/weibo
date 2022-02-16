<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * 用户间关注/取消关注
 * Class FollowersController
 *
 * @package App\Http\Controllers
 */
class FollowersController extends Controller
{
    public function __construct()
    {
        // 用户必须登录
        $this->middleware('auth');
    }

    /**
     * 关注用户
     *
     * @param User $user 当前被
     */
    public function store(User $user)
    {
        // 验证自己不能关注自己
        $this->authorize('follow', $user);

        if (! Auth::user()->isFollowing($user->id)) {
            Auth::user()->follow($user->id);
        }

        return redirect()->route('users.show', $user->id);
    }

    /**
     * 取消关注用户
     *
     * @param User $user
     */
    public function destroy(User $user)
    {
        $this->authorize('follow', $user);

        if (Auth::user()->isFollowing($user->id)) {
            Auth::user()->unfollow($user->id);
        }

        return redirect()->route('users.show', $user->id);
    }
}
