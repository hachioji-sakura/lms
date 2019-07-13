@extends('layouts.loginbox')
@section('title', __('labels.recess').__('labels.contact'))
@section('title_header', __('labels.recess').__('labels.contact'))

@section('content')
<div class="alert alert-warning text-sm pr-2">
  <h5><i class="icon fa fa-exclamation-triangle"></i> {{__('labels.important')}}</h5>
  {!!nl2br(__('messages.warning_recess'))!!}
  <br>
  <br>
  {!!nl2br(__('messages.warning_recess2'))!!}
</div>
<form id="recess_form" method="POST" action="">
    @csrf
    @component('asks.forms.recess_form', ['item' => $item, 'domain'=>$domain])
    @endcomponent
    <div class="form-group row mb-3">
      <div class="col-12">
        <button type="button" class="btn btn-submit btn-primary btn-block" confirm="{{__('messages.confirm_recess')}}">
          {{ __('labels.send_button') }}
        </button>
      </div>
    </div>
</form>

@endsection
