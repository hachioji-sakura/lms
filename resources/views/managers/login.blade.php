@extends('layouts.loginbox')
@section('title', '管理者ログイン')
@section('title_header', '管理者ログイン')
@section('content')
<form id="login_form" method="POST" action="{{ route('login') }}">
    @csrf
    @component('auth.login.login_form', [])
    @endcomponent
    <div class="form-group row mb-3">
        <div class="col-12">
            <button type="button" class="btn btn-submit btn-danger btn-block">
                ログイン
            </button>
        </div>
    </div>
</form>
<div class="my-2 row hr-1 bd-gray">
  <h6 class="col-12">
  	<a href="/forget" class="small mr-2"><i class="fa fa-arrow-alt-circle-right mr-1"></i>パスワード忘れた方</a>
    <a href="/login" class="small mr-2 float-right "><i class="fa fa-user-tie mr-1"></i>講師ログインページへ</a>
  </h6>
</div>

@endsection
