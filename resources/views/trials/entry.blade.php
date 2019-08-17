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
      {!!nl2br(__('messages.trial_entry1'))!!}
  <br><br>
      {!!nl2br(__('messages.trial_entry2'))!!}
      @endif
    </h4>
  @else
  <form method="POST"  action="/entry">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div id="trials_entry" class="carousel slide" data-ride="carousel" data-interval="false">
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
          @yield('lesson_week_form')
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
          @component('trials.forms.confirm_form', ['attributes' => $attributes]) @endcomponent
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create">
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
  var form_data = util.getLocalData('trials_entry');
  base.pageSettinged("trials_entry", form_data);
  grade_select_change();
  $('#trials_entry').carousel({ interval : false});
  if(form_data && !util.isEmpty(form_data['student2_name_last'])){
    $('.student2').collapse('show');
  }
  if(form_data && !util.isEmpty(form_data['student3_name_last'])){
    $('.student3').collapse('show');
  }
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('trials_entry .carousel-item.active')){
      util.removeLocalData('trials_entry');
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    var form_data = front.getFormValue('trials_entry');
    if(front.validateFormValue('trials_entry .carousel-item.active')){
      util.setLocalData('trials_entry', form_data);
      $('body, html').scrollTop(0);
      $('#trials_entry').carousel('next');
      $('#trials_entry').carousel({ interval : false});
    }

    $("ol.step li").removeClass("is-current");
    if($(this).hasClass('btn-confirm')){
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
    $('#trials_entry').carousel('prev');
    $('#trials_entry').carousel({ interval : false});
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    form_data["email"] = $("input[name=email]").val();

    if(form_data["trial_date1"] && form_data["trial_start_time1"] && form_data["trial_end_time1"]){
      var trial_start = $('select[name=trial_start_time1] option:selected').text().trim();
      var trial_end = $('select[name=trial_end_time1] option:selected').text().trim();
      form_data["trial_date_time1"] = util.dateformat(form_data["trial_date1"], '%m月%d日')+'<br>'+trial_start+" ～ "+trial_end;
    }
    if(form_data["trial_date2"] && form_data["trial_start_time2"] && form_data["trial_end_time2"]){
      var trial_start = $('select[name=trial_start_time2] option:selected').text().trim();
      var trial_end = $('select[name=trial_end_time2] option:selected').text().trim();
      form_data["trial_date_time2"] = util.dateformat(form_data["trial_date2"], '%m月%d日')+'<br>'+trial_start+" ～ "+trial_end;
    }
    if(form_data["trial_date3"] && form_data["trial_start_time3"] && form_data["trial_end_time3"]){
      var trial_start = $('select[name=trial_start_time3] option:selected').text().trim();
      var trial_end = $('select[name=trial_end_time3] option:selected').text().trim();
      form_data["trial_date_time3"] = util.dateformat(form_data["trial_date3"], '%m月%d日')+'<br>'+trial_start+" ～ "+trial_end;
    }

    var _names = ["lesson", "lesson_place", "howto", "kids_lesson", "english_talk_lesson"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          var t = $(this).parent().parent().text().trim();
          t = t.replace_all('[MAP]', '');
          form_data[value+"_name"] += t+'<br>';
        });
      }
    });

    var _names = ["grade", "student2_grade", "student3_grade"];
    $.each(_names, function(index, value) {
      if(form_data[value]){
        var _name = $('select[name='+value+'] option:selected').text().trim();
        form_data[value+"_name"] = _name;
      }
    });

    _names = ["english_teacher", "piano_level",
              "english_talk_course_type", "kids_lesson_course_type",
              "course_minutes", "lesson_week_count",
              "gender",
              "student2_gender",
              "student3_gender"
            ];
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
