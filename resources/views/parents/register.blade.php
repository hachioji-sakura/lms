@extends('layouts.simplepage')
@section('title', '入会お申込み')

@if(empty($result))
  @section('title_header')
  <ol class="step">
    <li id="step_input" class="is-current">ご入力</li>
    <li id="step_confirm">ご確認</li>
    <li id="step_complete">完了</li>
  </ol>
  @endsection
  @include($domain.'.create_form')
@else
  @section('title_header')
  <ol class="step">
    <li id="step_input">ご入力</li>
    <li id="step_confirm">ご確認</li>
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
    <div id="parents_register" class="carousel slide" data-ride="carousel" data-interval="false">
      <input type="hidden" name="email" value="{{$parent->email}}" />
      <input type="hidden" name="access_key" value="{{$access_key}}" />
      <input type="hidden" name="parent_id" value="{{$parent->id}}" />
      <input type="hidden" name="trial_id" value="{{$trial->id}}" />
      <input type="hidden" name="student_id" value="{{$student->id}}" />
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('parent_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                次へ
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('student_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                次へ
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
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                次へ
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
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
                <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1 btn-confirm">
                  <i class="fa fa-check-circle mr-1"></i>
                  内容確認
                </a>
            </div>
          </div>
        </div>
        <div class="carousel-item" id="confirm_form">
          @yield('confirm_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
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
  var form_data = util.getLocalData('parents_register');
  base.pageSettinged("parents_register", form_data);
  $('#parents_register').carousel({ interval : false});
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('parents_register .carousel-item.active')){
      util.removeLocalData('parents_register');
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('parents_register .carousel-item.active')){
      $('#parents_register').carousel('next');
      $('#parents_register').carousel({ interval : false});
    }
    $("ol.step li").removeClass("is-current");
    if($(this).hasClass('btn-confirm')){
      var form_data = front.getFormValue('parents_register');
      util.setLocalData('parents_register', form_data);
      base.pageSettinged("confirm_form", form_data_adjust(form_data));
      $("ol.step #step_confirm").addClass("is-current");
    }
    else {
      $("ol.step #step_input").addClass("is-current");
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#parents_register').carousel('prev');
    $('#parents_register').carousel({ interval : false});
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){

    form_data["email"] = $("input[name=email]").val();
    if(form_data["grade"]){
      form_data["grade_name"] = $('select[name=grade] option:selected').text().trim();
    }

    var _names = ["lesson", "lesson_place", "howto",];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });
    _names = ["english_teacher", "piano_level"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value]){
        $("input[name='"+value+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });
    $("input.week_time[type='checkbox'][value!='disabled']:checked").each(function(index, value){
      var val = $(this).val();
      var name = $(this).attr("name");
      name = name.replace('[]', '');
      form_data[name+"_"+val+"_name"] = "〇";
    });
    form_data["subject_level_name"] = "";
    $("input.subject_level[type='radio'][value!=1]:checked").each(function(index, value){
      var val = $(this).val();
      var name = $(this).attr("name");
      name = name.replace('[]', '');
      form_data[name+"_"+val+"_name"] = "〇";
    });
    return form_data;
  }
});
</script>
@endif
@endsection
