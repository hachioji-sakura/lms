@extends('layouts.loginbox')
@section('title', __('labels.unsubscribe').__('labels.contact'))
@section('title_header', __('labels.unsubscribe').__('labels.contact'))

@section('content')
@if($item->role=='parent')
<form id="unsubscribe_form" method="POST" action="/unsubscribe">
  @method('GET')
  <div class="row mb-3">
    <div class="col-12 mb-2">
      <label for="name" class="w-100">
        {{__('labels.students')}}{{__('labels.name')}}
      </label>
      @if(count($item->relations) > 0)
      <select name="student_id" class="form-control select2" required="true">
        @foreach($item->relations as $relation)
         <option value="{{ $relation->student->id }}">
           {{$relation->student->name()}}
         </option>
        @endforeach
      </select>
      @endif
    </div>
    <div class="col-12 mb-2">
      <button type="submit" class="btn btn-submit btn-primary btn-block">
        {{__('labels.students').__('labels.select')}}
      </button>
    </div>
  </div>
</form>
@else
  @if($already_data==null && $recess_data==null)
  {{-- 新規登録可能 --}}
  <div class="alert alert-warning text-sm pr-2">
    <h5><i class="icon fa fa-exclamation-triangle"></i> {{__('labels.important')}}</h5>
    {!!nl2br(__('messages.warning_unsubscribe'))!!}
  </div>
  <form id="unsubscribe_form" method="POST" action="/asks">
    @csrf
    @component('asks.forms.unsubscribe_form', ['item' => $item, 'domain'=>$domain, 'user' => $user, 'already_data' => $already_data])
    @endcomponent
  @elseif($already_data!=null && $recess_data==null)
  {{-- 退会の登録がある --}}
  <form id="unsubscribe_form" method="POST" action="/asks/{{$already_data->id}}/status_update/cancel">
    @csrf
    @method('PUT')
    @component('asks.forms.unsubscribe_form', ['item' => $item, 'domain'=>$domain, 'user' => $user, 'already_data' => $already_data])
    @endcomponent
  @else
  {{-- 休会の登録がある --}}
  <div class="alert alert-secondary text-sm pr-2">
    休会連絡が登録されています。<br>
    休会連絡をキャンセル後、休会連絡を送信してください。
  </div>
  @endif

  <div class="form-group row mb-3">
    <div class="col-12 mb-2">
    @if($recess_data!=null)
    <a class="btn btn-block btn-secondary" href="/{{$domain}}/{{$item->id}}/recess" >
      {{__('labels.recess')}}{{__('labels.asks')}}{{__('labels.cancel_button')}}する
    </a>
    @elseif($already_data!=null)
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
  </div>
</form>
@endif
<div class="row mb-3">
  <div class="col-12 mb-2">
    <a class="btn btn-block btn-default" href="/" >
      <i class="fa fa-arrow-right mr-1"></i>
      {{__('labels.top')}}へ
    </a>
  </div>
</div>
<div class="my-2 row hr-1 bd-gray">
  <h6 class="col-12">
    <a href="/{{$domain}}/{{$item->id}}/recess" class="small mr-2"><i class="fa fa-arrow-alt-circle-right mr-1"></i>
      {{ __('labels.to_recess') }}
    </a>
  </h6>
</div>

@endsection
