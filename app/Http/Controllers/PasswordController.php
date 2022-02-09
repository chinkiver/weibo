<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    /**
     * 显示找回密码页面
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * 向用户输入的邮箱发送 token 邮件（前提得存在该邮箱的用户）
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 验证输入的邮箱
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        // 获取对应的用户
        $user = User::where('email', $email)->first();

        // 判断用户是否存在
        if (is_null($user)) {
            session()->flash('danger', '用户不存在！');
            return redirect()->back()->withInput();
        }

        // 生成 Token
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));

        // 入库，使用 updateOrInsert 来保持 Email 唯一
        DB::table('password_resets')->updateOrInsert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => new Carbon(),
        ]);

        $emailView = 'emails.reset_link';

        // 通过邮件发送 Token
        Mail::send($emailView, compact('token'), function ($message) use ($email) {
            $message->to($email)->subject('忘记密码');
        });

        session()->flash('success', '密码重置邮件，已发送成功！');
        return redirect()->back();
    }

    /**
     * 根据 token 显示密码重置页面
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showResetForm(Request $request)
    {
        // 从路由中获取 token 的值
        $token = $request->route()->parameter('token');

        return view('auth.passwords.reset', compact('token'));
    }

    /**
     * 重置密码
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        // 验证输入
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $email = $request->email;
        $token = $request->token;

        // 设定有限时间
        $expires = 60 * 100;

        // 获取邮箱用户（这里解释了为何又需要再次输入邮箱）
        $user = User::where('email', $email)->first();

        if (is_null($user)) {
            session()->flash('danger', '此用户不存在！');
            return redirect()->back()->withInput();
        }

        // 从密码重置表中读取该 email 的记录
        $passwordReset = (array) DB::table('password_resets')->where('email', $email)->first();

        if ($passwordReset) {
            // 检查是否过期
            if (Carbon::parse($passwordReset['created_at'])->addSeconds($expires)->isPast()) {
                session()->flash('danger', '链接已过期，请重新尝试');
                return redirect()->back();
            }

            // 检查是否正确
            if (! Hash::check($token, $passwordReset['token'])) {
                session()->flash('danger', '令牌错误');
                return redirect()->back();
            }

            // 更新用户密码
            $user->update([
                'password' => bcrypt($request->password),
            ]);

            // 返回
            session()->flash('success', '密码重置成功！');
            return redirect()->route('login');
        }

        // 不存在的重置记录
        session()->flash('danger', '未找到重置记录！');
        return redirect()->back();
    }
}
