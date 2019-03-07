@extends('layouts.simplepage')
@section('title', '体験授業お申し込フォーム')

@if(empty($result))
  @section('title_header')
  <ol class="step">
    <li id="step_input" class="is-current">ご入力</li>
    <li id="step_confirm">ご確認</li>
    <li id="step_complete">完了</li>
  </ol>
  @endsection
  @include('trials.create_form')
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
    <h4 class="bg-success p-3 text-sm">
      @if($result==="success")
      体験授業をお申し込みいただきまして、<br>
      誠にありがとうございます。<br><br>
      ２営業日以内に、当塾よりご連絡いたしますので、<br>
      何卒宜しくお願い致します。
      @else
      体験授業をお申し込みいただきまして、<br>
      誠にありがとうございます。<br><br>
      ２営業日以内に、当塾よりご連絡いたしますので、<br>
      何卒宜しくお願い致します。
      @endif
    </h4>
  @else
  <form method="POST"  action="/entry">
    @csrf
    <div id="trial_form" class="carousel slide" data-ride="carousel" data-interval=false>
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('trial_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                次へ
                <i class="fa fa-arrow-circle-right ml-1"></i>
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
                次へ
                <i class="fa fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('subject_form')
          @yield('survey_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
                <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1 btn-confirm">
                  <i class="fa fa-file-alt mr-1"></i>
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
                    この内容でお申込み
                    <i class="fa fa-caret-right ml-1"></i>
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
  var form_data = util.getLocalData('trial_form');
  base.pageSettinged("trial_form", form_data);

  //submit
  $("button[type='submit']").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('trial_form .carousel-item.active')){
      //util.removeLocalData('trial_form');
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('trial_form .carousel-item.active')){
      $('body, html').scrollTop(0);
      $('#trial_form').carousel('next');
    }

    $("ol.step li").removeClass("is-current");
    if($(this).hasClass('btn-confirm')){
      var form_data = front.getFormValue('trial_form');
      util.setLocalData('trial_form', form_data);
      base.pageSettinged("confirm_form", form_data_adjust(form_data));
      $("ol.step #step_confirm").addClass("is-current");
    }
    else {
      $("ol.step #step_input").addClass("is-current");
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('body, html').scrollTop(0);
    $('#trial_form').carousel('prev');
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    form_data["email"] = $("input[name=email]").val();
    if(form_data["gender"]){
      form_data["gender_name"] = $("label[for='"+$("input[name='gender']:checked").attr("id")+"']").text().trim();
    }
    if(form_data["grade"]){
      var grade_name = $('select[name=grade] option:selected').text().trim();
      form_data["grade_name"] = grade_name;
    }
    if(form_data["trial_date1"] && form_data["trial_start_time1"] && form_data["trial_end_time1"]){
      var trial_start = $('select[name=trial_start_time1] option:selected').text().trim();
      var trial_end = $('select[name=trial_end_time1] option:selected').text().trim();
      form_data["trial_date_time1"] = form_data["trial_date1"]+" "+ trial_start+" ～ "+trial_end+"";
    }
    if(form_data["trial_date2"] && form_data["trial_start_time2"] && form_data["trial_end_time2"]){
      var trial_start = $('select[name=trial_start_time2] option:selected').text().trim();
      var trial_end = $('select[name=trial_end_time2] option:selected').text().trim();
      form_data["trial_date_time2"] = form_data["trial_date2"]+" "+ trial_start+" ～ "+trial_end+"";
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
