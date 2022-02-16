<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 头像
     *
     * @param int $size
     *
     * @return string
     */
    public function gravatar($size = 100):string
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
//        return "http://www.gravatar.com/avatar/$hash?s=$size";

        return asset('images/grapes.png');
    }

    /**
     * 用户所发布的多条微博
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    /**
     * 展示微博列表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feed()
    {
        // 获取关注用户 ID 列表
        $userIds = $this->followings->pluck('id')->toArray();

        // 将当前用户 ID 加入到关注用户列表
        array_push($userIds, $this->id);

        return Status::whereIn('user_id', $userIds)
            ->with('user') // 所关联的用户对象
            ->orderBy('created_at', 'desc');
    }

    /**
     * 用户所拥有的多个粉丝
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    /**
     * 用户所关注的多个用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    /**
     * 关注用户操作
     *
     * @param mixed $userIds 关注的用户列表
     */
    public function follow($userIds)
    {
        if (! is_array($userIds)) {
            $userIds = compact('userIds');
        }

        $this->followings()->sync($userIds, false);
    }

    /**
     * 取消关注操作
     *
     * @param mixed $userIds 关注的用户列表
     */
    public function unfollow($userIds)
    {
        if (! is_array($userIds)) {
            $userIds = compact('userIds');
        }

        $this->followings()->detach($userIds);
    }

    /**
     * 该用户是否已经关注了某个用户判断
     *
     * @param int $userId 被关注人
     *
     * @return mixed
     */
    public function isFollowing($userId)
    {
        return $this->followings->contains($userId);
    }
}
