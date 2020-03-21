{{--
@extends('layouts.simplepage')
@section('title', '体験申し込みページ')

@if(empty($result))
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
    <div id="parents_entry" class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('parent_form')
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
          @yield('trial_form')
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
  var form_data = util.getLocalData('parents_entry');
  base.pageSettinged("parents_entry", form_data);
  $('#parents_entry').carousel({ interval : false});

  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('parents_entry .carousel-item.active')){
      util.removeLocalData('parents_entry');
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    var form_data = front.getFormValue('parents_entry');
    form_data = form_data_adjust(form_data);
    if(front.validateFormValue('parents_entry .carousel-item.active')){
      util.setLocalData('parents_entry', form_data);
      base.pageSettinged("confirm_form", form_data);
      $('body, html').scrollTop(0);
      $('#parents_entry').carousel('next');
      $('#parents_entry').carousel({ interval : false});
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('body, html').scrollTop(0);
    $('#parents_entry').carousel('prev');
    $('#parents_entry').carousel({ interval : false});
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

    if(form_data["trial_date1"] && form_data["trial_time1"]){
      var trial_time_name = $('select[name=trial_time1] option:selected').text().trim();
      form_data["trial_date_time1"] = util.dateformat(form_data["trial_date1"], '%m月%d日')+'<br>'+trial_time_name;
    }
    if(form_data["trial_date2"] && form_data["trial_time2"]){
      var trial_time_name = $('select[name=trial_time2] option:selected').text().trim();
      form_data["trial_date_time2"] = util.dateformat(form_data["trial_date2"], '%m月%d日')+'<br>'+trial_time_name;
    }
    if(form_data["trial_date3"] && form_data["trial_time3"]){
      var trial_time_name = $('select[name=trial_time3] option:selected').text().trim();
      form_data["trial_date_time3"] = util.dateformat(form_data["trial_date3"], '%m月%d日')+'<br>'+trial_time_name;
    }
/*TODO 後まわし
    if(form_data["trial_date4"] && form_data["trial_time4"]){
      var trial_time_name = $('select[name=trial_time4] option:selected').text().trim();
      form_data["trial_date_time4"] = util.dateformat(form_data["trial_date4"], '%m月%d日')+'<br>'+trial_time_name;
    }
    if(form_data["trial_date5"] && form_data["trial_time5"]){
      var trial_time_name = $('select[name=trial_time5] option:selected').text().trim();
      form_data["trial_date_time5"] = util.dateformat(form_data["trial_date5"], '%m月%d日')+'<br>'+trial_time_name;
    }
*/
    var _names = ["lesson_place", "howto"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
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
--}}
