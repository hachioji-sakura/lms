@extends('layouts.simplepage')
@section('title', 'ユーザー登録')

@if(empty($result))
@include('students.create_form')
@include('parents.create_form')
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
          <a href="/students/entry" role="button" class="btn btn-outline-success btn-block float-left mr-1">
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
          <a href="/logout" role="button" class="btn btn-secondary btn-block float-left mr-1">
            ログアウトする
          </a>
        </p>
    </div>
  </div>
  @endif
@else
  <form method="POST"  action="/register">
    @csrf
    <div id="parents_add_form" class="carousel slide" data-ride="carousel" data-interval=false>
      <input type="hidden" name="access_key" value="{{$access_key}}" />
      <input type="hidden" name="parent_id" value="{{$parent->id}}" />
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
          @yield('account_form')
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
          @yield('survey_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
                <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
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
                <button type="submit" class="btn btn-primary btn-block" accesskey="students_create">
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
  var form_data = util.getLocalData('parents_add_form');
  base.pageSettinged("parents_add_form", form_data);

  //submit
  $("button[type='submit']").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('parents_add_form .carousel-item.active')){
      util.setLocalData('parents_add_form', "");
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('parents_add_form .carousel-item.active')){
      var form_data = front.getFormValue('parents_add_form');
      util.setLocalData('parents_add_form', form_data);
      base.pageSettinged("confirm_form", form_data_adjust(form_data));
      $('#parents_add_form').carousel('next');
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#parents_add_form').carousel('prev');
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    form_data["email"] = $("input[name=email]").val();
    if(form_data["gender"]){
      form_data["gender_name"] = $("label[for='"+$("input[name='gender']:checked").attr("id")+"']").text().trim();
    }
    if(form_data["grade"]){
      form_data["grade_name"] = $('select[name=grade] option:selected').text().trim();
    }
    var _names = ["lesson_subject", "lesson_week", "lesson_time", "lesson_time_holiday", "lesson_place", "howto"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });
    return form_data;
  }
});
</script>
@endif
@endsection
