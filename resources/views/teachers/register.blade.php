@extends('layouts.simplepage')
@section('title', $domain_name.'登録')

@if(empty($result))
  @section('title_header')
  <ol class="step">
    <li id="step_input" class="is-current">{{__('labels.input')}}</li>
    <li id="step_complete">{{__('labels.complete')}}</li>
  </ol>
  @endsection
  @include($domain.'.create_form')
@else
  @section('title_header')
  <ol class="step">
    <li id="step_input">{{__('labels.input')}}</li>
    <li id="step_complete" class="is-current">{{__('labels.complete')}}</li>
  </ol>
  @endsection
@endif

@section('content')
<div id="{{$domain}}_register" class="direct-chat-msg">
@if(!empty($result))
    @if($result==='token_error')
    <div class="row">
      <div class="col-12">
        <h4 class="bg-danger p-3 text-sm">
          このページの有効期限が切れています。<br>
          再度、{{$domain_name}}仮登録ページより仮登録を行ってください。
        </h4>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <p class="my-2">
          <a href="/{{$domain}}/entry" role="button" class="btn btn-outline-success btn-block float-left mr-1">
            {{$domain_name}}仮登録はこちら
          </a>
        </p>
    </div>
  </div>
  @elseif($result==='logout')
    <div class="row">
      <div class="col-12">
        <h4 class="bg-danger p-3 text-sm">
          ログイン状態が残っています。<br>
          ログアウトしてください。
        </h4>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <p class="my-2">
          <a href="/logout?back=1" role="button" class="btn btn-secondary btn-block float-left mr-1">
            {{__('labels.logout_button')}}
          </a>
        </p>
    </div>
  </div>
  @endif
@else
  <form method="POST"  action="/{{$domain}}/register">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div id="teachers_register" class="carousel slide" data-ride="carousel" data-interval="false">
      <input type="hidden" name="access_key" value="{{$access_key}}" />
      <input type="hidden" name="id" value="{{$item->id}}" />
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('item_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                {{__('labels.next_button')}}
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('account_form')
          @yield('bank_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                {{__('labels.back_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                {{__('labels.next_button')}}
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('lesson_week_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                {{__('labels.back_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                {{__('labels.next_button')}}
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('subject_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                {{__('labels.back_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="teachers_register">
                  <i class="fa fa-plus-circle mr-1"></i>
                  {{__('labels.add_button')}}
                </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<script>

$(function(){
  base.pageSettinged("teachers_register", []);
  $('#teachers_register').carousel({ interval : false});
  lesson_checkbox_change();
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('teachers_register .carousel-item.active')){
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('teachers_register .carousel-item.active')){
      var form_data = front.getFormValue('teachers_register');
      $('body, html').scrollTop(0);
      $('#teachers_register').carousel('next');
      $('#teachers_register').carousel({ interval : false});

    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('body, html').scrollTop(0);
    $('#teachers_register').carousel('prev');
    $('#teachers_register').carousel({ interval : false});
  });
});
</script>
@endif
@endsection
