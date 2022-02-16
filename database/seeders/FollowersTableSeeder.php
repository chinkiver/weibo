<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 获取全部用户
        $users = User::all();

        // 获取第一个用户（管理员）
        $user = $users->first();
        $userId = $user->id;

        // 获取去掉 ID 为 1 的所有用户 ID 列表
        $followers = $users->slice(1);
        $followerIds = $followers->pluck('id')->toArray();

        // 1 号用户关注 $followerIds
        $user->follow($followerIds);

        // $followerIds 都关注 1 号用户
        foreach ($followers as $follower) {
            $follower->follow($userId);
        }
    }
}
