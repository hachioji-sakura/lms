@extends('layouts.loginbox')
@section('title', __('labels.unsubscribe').__('labels.contact'))
@section('title_header', __('labels.unsubscribe').__('labels.contact'))

@section('content')
<div class="alert alert-warning text-sm pr-2">
  <h5><i class="icon fa fa-exclamation-triangle"></i> {{__('labels.important')}}</h5>
  {!!nl2br(__('messages.warning_unsubscribe'))!!}
</div>
@if($already_data==null)
<form id="unsubscribe_form" method="POST" action="/asks">
@else
<form id="recess_form" method="POST" action="/asks/{{$already_data->id}}/status_update/cancel">
  @method('PUT')

@endif
    @csrf
    @component('asks.forms.unsubscribe_form', ['item' => $item, 'domain'=>$domain, 'user' => $user, 'already_data' => $already_data])
    @endcomponent

  <div class="form-group row mb-3">
    <div class="col-12 mb-2">
    @if($already_data!=null)
        <button type="button" class="btn btn-submit btn-primary btn-block" confirm="{{__('messages.confirm_unsubscribe_cancel')}}">
          <i class="fa fa-ban mr-1"></i>
          {{ __('labels.unsubscribe')}}{{__('labels.asks')}}{{__('labels.cancel_button')}}
        </button>
    @else
        <button type="button" class="btn btn-submit btn-primary btn-block" confirm="{{__('messages.confirm_unsubscribe')}}">
          <i class="fa fa-envelope mr-1"></i>
          {{ __('labels.send_button') }}
        </button>
    @endif
    </div>
    <div class="col-12 mb-2">
      <a class="btn btn-block btn-secondary" href="/" >
        {{__('labels.top')}}へ
      </a>
    </div>
  </div>

</form>
<div class="my-2 row hr-1 bd-gray">
  <h6 class="col-12">
    <a href="/{{$domain}}/{{$item->id}}/recess" class="small mr-2"><i class="fa fa-arrow-alt-circle-right mr-1"></i>
      {{ __('labels.to_recess') }}
    </a>
  </h6>
</div>

@endsection
