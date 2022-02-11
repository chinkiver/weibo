<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 微博
 * Class StatusesController
 *
 * @package App\Http\Controllers
 */
class StatusesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 发布微博
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:140',
        ]);

        Auth::user()->statuses()->create([
            'content' => $request['content'],
        ]);

        session()->flash('success', '发布成功！');
        return redirect()->back();
    }

    /**
     * 删除微博
     *
     * @param Status $status
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Status $status)
    {
        // Policy 验证
        $this->authorize('destroy', $status);

        // 删除微博
        $status->delete();

        session()->flash('success', '微博已被成功删除！');
        return redirect()->back();
    }
}
