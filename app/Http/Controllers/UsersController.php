<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 用户管理
 * Class UsersController
 *
 * @package App\Http\Controllers
 */
class UsersController extends Controller
{
    public function __construct()
    {
        // 除了 show、create、store 方法外，其他都需要登录
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index'],
        ]);

        // 访客可以访问注册页面
        $this->middleware('guest', [
            'only' => ['create'],
        ]);
    }

    /**
     * 注册用户页面
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('users.create');
    }

    public function index()
    {
        // 分页
        $users = User::paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * 显示用户详细
     *
     * @param User $user
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * 注册用户
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // 输入验证
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6',
        ]);

        // 保存用户
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        // 直接登录
        Auth::login($user);

        // 提示
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程！');

        return redirect()->route('users.show', ['user' => $user]);
    }

    /**
     * 显示用户编辑页面
     *
     * @param User $user
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(User $user)
    {
        // 验证是否可以更新，用户只能更新自己的信息
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        // 验证是否可以更新，用户只能更新自己的信息
        $this->authorize('update', $user);

        // 输入检查
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|max:255',
        ]);

        $data = [];
        $data['name'] = $request->name;

        if ($request->has('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash('success', '用户信息更新成功！');
        return redirect()->route('users.show', $user);
    }
}
