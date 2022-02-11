@extends('layouts.default')

@section('title', '首页')

@section('content')

  @if(Auth::check())

    <div class="row">
      <div class="col-md-8">
        <section class="status_form">
          @include('shared._status_form')
        </section>

        <hr>
        <h4>微博列表</h4>
        @include('shared._feed')
      </div>

      <aside class="col-md-4">
        <section class="user_info">
          @include('shared._user_info', ['user' => Auth::user()])
        </section>
      </aside>
    </div>

  @else

    <div class="p-5 mb-4 bg-light rounded-3">
      <div class="container-fluid py-5" style="background-color: #ced4da">
        <h1 class="display-5 fw-bold">Hello Laravel</h1>
        <p class="col-md-8 fs-4">
          你现在所看到的是 <a href="https://learnku.com/courses/laravel-essential-training">Laravel 入门教程</a> 的示例项目主页。
        </p>
        <p class="col-md-8 fs-4">
          一切，将从这里开始。
        </p>
        <p class="col-md-8 fs-4">
          <a class="btn btn-lg btn-success" href="{{ route('signup') }}" role="button">现在注册</a>
        </p>
      </div>
    </div>

  @endif

@stop
