@extends('layouts.simplepage')
@section('title', $domain_name.'登録')
@if(empty($result))
  @section('title_header')
  <ol class="step">
    <li id="step_input" class="is-current">ご入力</li>
    <li id="step_complete">完了</li>
  </ol>
  @endsection
  @include($domain.'.create_form')
@else
  @section('title_header')
  <ol class="step">
    <li id="step_input">ご入力</li>
    <li id="step_complete" class="is-current">完了</li>
  </ol>
  @endsection
@endif

@section('content')
<div id="students_register" class="direct-chat-msg">
@if(!empty($result))
    @if($result==='token_error')
    <div class="row">
      <div class="col-12">
        <h4 class="bg-danger p-3 text-sm">
          このページの有効期限が切れています。<br>
          再度、申し混みページより仮登録を行ってください。
        </h4>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <p class="my-2">
          <a href="/entry" role="button" class="btn btn-outline-success btn-block float-left mr-1">
            入会お申込みはこちら
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
            ログアウトする
          </a>
        </p>
    </div>
  </div>
  @endif
@else
  <form method="POST"  action="/register">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div id="parents_register" class="carousel slide" data-ride="carousel" data-interval="false">
      <input type="hidden" name="email" value="{{$parent->email}}" />
      <input type="hidden" name="access_key" value="{{$access_key}}" />
      <input type="hidden" name="parent_id" value="{{$parent->id}}" />
      <input type="hidden" name="trial_id" value="{{$trial->id}}" />
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('parent_form')
          <div class="row">
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
                  <i class="fa fa-plus-circle mr-1"></i>
                    登録する
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
  base.pageSettinged("parents_register", null);
  $('#parents_register').carousel({ interval : false});
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('parents_register .carousel-item.active')){
      util.removeLocalData('parents_register');
      $("form").submit();
    }
  });

});
</script>
@endif
@endsection
