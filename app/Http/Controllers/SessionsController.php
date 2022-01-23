<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    /**
     * 显示用户登陆页面
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create ()
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
    public function store (Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (! Auth::attempt($credentials, $request->has('remember'))) {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }

        session()->flash('success', '欢迎回来！');
        return redirect()->route('users.show', Auth::user());
    }

    /**
     * 退出
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy ()
    {
        \Log::debug('登出');
        Auth::logout();

        session()->flash('success', '您已成功退出！');
        return redirect()->route('login');
    }
}
