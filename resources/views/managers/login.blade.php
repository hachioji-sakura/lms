@extends('layouts.loginbox')
@section('title', __('labels.admin_page'))
@section('title_header', __('labels.admin_page'))
@section('content')
<form id="login_form" method="POST" action="{{ route('login') }}">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @component('auth.login.login_form', [])
    @endcomponent
    <div class="form-group row mb-3">
        <div class="col-12">
            <button type="button" class="btn btn-submit btn-danger btn-block">
              {{ __('labels.login') }}
            </button>
        </div>
    </div>
</form>
<div class="my-2 row hr-1 bd-gray">
  <h6 class="col-12">
    @component('auth.login.forget_link', []) @endcomponent
    <a href="/login" class="small mr-2 float-right "><i class="fa fa-sign-in-alt mr-1"></i>
      {{__('labels.to_login')}}
    </a>
  </h6>
</div>

@endsection
