@section('title')
  {{$domain_name}}詳細
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section class="content-header">
	<div class="container-fluid" id="trial_to_calendar">
    @if($select_teacher_id > 0)
    <form method="POST"  action="/{{$domain}}/{{$item->id}}/to_calendar">
      @csrf
      <input type="text" name="dummy" style="display:none;" / >
      <input type="hidden" name="teacher_id" value="{{$candidate_teachers[0]->id}}">
      <input type="hidden" name="lesson" value="{{$select_lesson}}">
      <input type="hidden" name="start_time" value="">
      <input type="hidden" name="end_time" value="">
      <input type="hidden" name="calendar_id" value="">
      <div class="row mb-1">
        <div class="col-md-4">
          @component('trials.forms.trial_detail',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent

        </div>
        <div class="col-md-8">
          <div class="card card-widget mb-2">
            <div class="card-header">
              <i class="fa fa-edit mr-1"></i>授業設定
            </div>
            <div class="card-footer">
              @component('trials.forms.charge_teacher', ['teacher' => $candidate_teachers[0], 'attributes' => $attributes, 'user' => $user,])
                @slot('addon')
                @endslot
              @endcomponent
              <div class="row">
                @component('calendar_settings.forms.course_type', ['item'=>$item, 'select_lesson' => $select_lesson, 'attributes' => $attributes]) @endcomponent
                @component('calendars.forms.add_type', ['item'=>$item,]) @endcomponent
                @component('trials.forms.select_trial_date', ['item'=>$item, 'candidate_teacher' => $candidate_teachers[0], 'attributes' => $attributes]) @endcomponent
                @if(count($candidate_teachers[0]->trial) < 1)
                <div class="col-6 mb-1">
                    <button type="button" class="btn btn-primary btn-block" disabled>
                      <i class="fa fa-check mr-1"></i>
                      体験授業予定を連絡する
                    </button>
                </div>
                <div class="col-6 mb-1">
                  <a href="/{{$domain}}/{{$item->id}}" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                    <i class="fa fa-arrow-circle-left mr-1"></i>
                    キャンセル
                  </a>
                </div>
                @else
                @component('calendar_settings.forms.charge_subject', ['item'=>$item, 'select_lesson' => $select_lesson, 'candidate_teacher' => $candidate_teachers[0], 'attributes' => $attributes]) @endcomponent
                @component('calendar_settings.forms.lesson_place_floor', ['item'=>$item, 'attributes' => $attributes]) @endcomponent
                @component('trials.forms.matching_decide', ['attributes' => $attributes]) @endcomponent
                <div class="col-6 mb-1">
                  <a href="/{{$domain}}/{{$item->id}}" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                    <i class="fa fa-arrow-circle-left mr-1"></i>
                    キャンセル
                  </a>
                </div>
                <div class="col-6 mb-1">
                    <button type="button" class="btn btn-submit btn-primary btn-block">
                      <i class="fa fa-check mr-1"></i>
                      体験授業予定を連絡する
                    </button>
                </div>
                @endif
              </div>
            </div>
          </div>

        </div>
      </div>
    </form>
    @else
      @component('trials.forms.select_teacher', ['item'=>$item,'select_lesson' => $select_lesson,'domain'=>$domain,'domain_name'=>$domain_name,'user'=>$user, 'candidate_teachers'=>$candidate_teachers, 'attributes' => $attributes, 'is_calendar_setting' => false]) @endcomponent
    @endif
  </div>
</section>
<script>
$(function(){
  base.pageSettinged("trial_to_calendar", null);
  $('#trial_to_calendar').carousel({ interval : false});
  //submit
  $("button.btn-submit").on('click', function(e){
    teacher_schedule_change();
    e.preventDefault();
    if(front.validateFormValue('trial_to_calendar')){
      $("#trial_to_calendar form").submit();
    }
  });

});
</script>
@endsection
