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
    public function gravatar($size = 100) : string
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
//        return "http://www.gravatar.com/avatar/$hash?s=$size";

        return asset('images/grapes.png');
    }

    /**
     * 用户可以发布多条微博
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
        return $this->statuses()->orderByDesc('created_at');
    }
}
