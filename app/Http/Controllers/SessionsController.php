<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 用户登录、推出的 Session 操作
 * Class SessionsController
 *
 * @package App\Http\Controllers
 */
class SessionsController extends Controller
{
    public function __construct()
    {
        // 只有访客可以访问登录页面
        $this->middleware('guest', [
            'only' => ['create'],
        ]);
    }

    /**
     * 显示用户登陆页面
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * 保存登录信息
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {

            if (Auth::user()->activated) {

            }

            session()->flash('success', '欢迎回来！');
            $fallback = route('users.show', Auth::user());
            return redirect()->intended($fallback);
        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }
    }

    /**
     * 退出
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        \Log::debug('登出');
        Auth::logout();

        session()->flash('success', '您已成功退出！');
        return redirect()->route('login');
    }
}
