<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 静态页
Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

// 用户注册
Route::get('signup', 'UsersController@create')->name('signup');

// 用户管理
Route::resource('users', 'UsersController');

// 用户登录/登出
Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');

// 注册后，激活邮箱
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

// 显示找回密码页面
Route::get('password/reset',  'PasswordController@showLinkRequestForm')->name('password.request');

// 向用户输入的邮箱发送 token 邮件（前提得存在该邮箱的用户）
Route::post('password/email',  'PasswordController@sendResetLinkEmail')->name('password.email');

// 根据 token 显示密码重置页面
Route::get('password/reset/{token}',  'PasswordController@showResetForm')->name('password.reset');

// 重置密码
Route::post('password/reset',  'PasswordController@reset')->name('password.update');

// 微博
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);
