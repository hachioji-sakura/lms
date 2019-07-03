@extends('layouts.loginbox')
@section('title', __('labels.login'))
@section('title_header', __('labels.login'))
@section('content')

<form id="login_form" method="POST" action="{{ route('login') }}">
    @csrf
    @component('auth.login.login_form', [])
    @endcomponent
    <div class="form-group row mb-3">
        <div class="col-12">
            <button type="button" class="btn btn-submit btn-primary btn-block">
              {{ __('labels.login') }}
            </button>
        </div>
    </div>
</form>
<div class="my-2 row hr-1 bd-gray">
  <h6 class="col-12">
  	<a href="/forget" class="small mr-2"><i class="fa fa-arrow-alt-circle-right mr-1"></i>
      {{ __('labels.password_forget') }}
    </a>
  </h6>
</div>
<div class="my-2 row">
  <a href="/entry" role="button" class="btn btn-outline-success btn-block btn-sm float-left mr-1">
    <i class="fa fa-sign-in-alt mr-1"></i>{{ __('labels.trial_entry') }}
  </a>
</div>
<div class="my-2 row">
  <h6 class="col-12">
  <a href="/managers/login" class="float-right small mr-2"><i class="fa fa-user-lock mr-1"></i>{{__('labels.admin_page')}}</a>
  </h6>
</div>
@endsection
