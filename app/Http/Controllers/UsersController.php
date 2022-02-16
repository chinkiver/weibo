<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail'],
        ]);

        // 访客可以访问注册页面
        $this->middleware('guest', [
            'only' => ['create'],
        ]);

        // 注册限流 一个小时内只能提交 10 次请求；
        $this->middleware('throttle:10,60', [
            'only' => ['store'],
        ]);
    }

    /**
     * 注册用户页面
     */
    public function create()
    {
        return view('users.create');
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
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // 直接登录
//        Auth::login($user);
//        return redirect()->route('users.show', ['user' => $user]);

        // 改称为需要验证邮件
        $this->sendEmailConfirmationTo($user);

        // 提示
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程！');
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }

    /**
     * 发送激活邮件
     *
     * @param User $user
     */
    private function sendEmailConfirmationTo(User $user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    /**
     * 用户列表
     */
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
     * @return \Illuminate\Contracts\View\View
     */
    public function show(User $user)
    {
        // 显示该用户所发布的微博
        $statuses = $user->statuses();

        if (! is_null($statuses)) {
            $statuses = $statuses->orderBy('created_at', 'desc')->paginate(10);
        }

        return view('users.show', compact('user', 'statuses'));
    }

    /**
     * 显示用户编辑页面
     *
     * @param User $user
     *
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $user)
    {
        // 验证是否可以更新，用户只能更新自己的信息
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * 更新用户信息
     *
     * @param User    $user
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
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

    /**
     * 用户激活邮件
     *
     * @param string $token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmEmail(string $token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = '';
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    /**
     * 显示用户的关注人列表
     */
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(10);
        $title = $user->name . ' 关注的人';

        return view('users.show_follow', compact('users', 'title'));
    }

    /**
     * 显示用户的粉丝列表
     */
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(10);
        $title = $user->name . ' 的粉丝';

        return view('users.show_follow', compact('users', 'title'));
    }
}
