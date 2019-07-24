@extends('layouts.loginbox')
@section('title', __('labels.recess').__('labels.contact'))
@section('title_header', __('labels.recess').__('labels.contact'))

@section('content')
@if($item->role=='parent')
<form id="recess_form" method="POST" action="/recess">
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
@if($already_data==null && $unsubscribe_data==null)
{{-- 新規登録可能 --}}
  <div class="alert alert-warning text-sm pr-2">
    <h5><i class="icon fa fa-exclamation-triangle"></i> {{__('labels.important')}}</h5>
    1. 休会は２ヶ月間することが可能です。
    但し、休会が明けた後、１ヶ月間は通塾することが条件となります。
    <br>
    2. また、休会の間、他の生徒様のご希望があった場合、現在通われている曜日及び時間に他の生徒様の予定が入ってしまうことがあります。
    <br>
  </div>
  <form id="recess_form" method="POST" action="/asks">
    @csrf
    @component('asks.forms.recess_form', ['item' => $item, 'domain'=>$domain, 'user' => $user, 'already_data' => $already_data])
    @endcomponent
  @elseif($already_data!=null && $unsubscribe_data==null)
  {{-- 休会の登録がある --}}
  <form id="recess_form" method="POST" action="/asks/{{$already_data->id}}/status_update/cancel">
    @method('PUT')
    @csrf
    @component('asks.forms.recess_form', ['item' => $item, 'domain'=>$domain, 'user' => $user, 'already_data' => $already_data])
    @endcomponent
  @else
  <div class="alert alert-secondary text-sm pr-2">
    退会連絡が登録されています。<br>
    退会連絡をキャンセル後、休会連絡を送信してください。
  </div>
  @endif

  <div class="form-group row mb-3">
    <div class="col-12 mb-2">
      @if($unsubscribe_data!=null)
      <a class="btn btn-block btn-secondary" href="/{{$domain}}/{{$item->id}}/unsubscribe" >
        <i class="fa fa-arrow-right mr-1"></i>
        {{__('labels.unsubscribe')}}{{__('labels.asks')}}{{__('labels.cancel_button')}}へ
      </a>
      @elseif($already_data!=null)
          <button type="button" class="btn btn-submit btn-primary btn-block" confirm="{{__('messages.confirm_recess_cancel')}}">
            <i class="fa fa-ban mr-1"></i>
            {{ __('labels.recess')}}{{__('labels.asks')}}{{__('labels.cancel_button')}}
          </button>
      @else
          <button type="button" class="btn btn-submit btn-primary btn-block" confirm="{{__('messages.confirm_recess')}}">
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

@endsection
