@section('title')
  {{$domain_name}}詳細
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
@include($domain.'.matching_form')


<section class="content-header">
	<div class="container-fluid" id="trial_to_calendar">
    @if($select_teacher_id > 0)
    <form method="POST"  action="/{{$domain}}/{{$item->id}}/confirm">
      @csrf
      @method('PUT')
      @yield('other_form')
      <div class="row">
        <div class="col-12 mb-1">
          <a href="/{{$domain}}/{{$item->id}}" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-arrow-circle-left mr-1"></i>
            キャンセル
          </a>
        </div>
        <div class="col-12 mb-1">
            <button type="button" class="btn btn-submit btn-primary btn-block">
              <i class="fa fa-check mr-1"></i>
              体験授業予定を連絡する
            </button>
        </div>
      </div>
    </form>
    @else
      @yield('teacher_select_form')
    @endif
  </div>
</section>
<script>
$(function(){
  var form_data = util.getLocalData('trials_confirm');
  base.pageSettinged("trials_confirm", form_data);
  $('#trials_confirm').carousel({ interval : false});
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('trial_to_calendar')){
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('trials_confirm .carousel-item.active')){
      var form_data = front.getFormValue('trials_confirm');
      util.setLocalData('trials_confirm', form_data);
      base.pageSettinged("confirm_form", form_data_adjust(form_data));
      $('#trials_confirm').carousel('next');
      $('#trials_confirm').carousel({ interval : false});
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#trials_confirm').carousel('prev');
    $('#trials_confirm').carousel({ interval : false});
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    if(form_data["place"]){
      form_data["place_name"] = $('select[name=place] option:selected').text().trim();
    }
    if(form_data["teacher_schedule"]){
      var _teacher_schedule = $('input[name=teacher_schedule]:checked');
      form_data["start_time"] = _teacher_schedule.attr('start_time');
      form_data["end_time"] = _teacher_schedule.attr('end_time');
    }

    var _names = ["matching_decide"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'[]']){
        $("input[name='"+value+'[]'+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });
    console.log(form_data);
    return form_data;
  }

});
</script>
@endsection
