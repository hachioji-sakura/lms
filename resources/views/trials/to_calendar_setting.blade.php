@section('title')
  {{$domain_name}}詳細
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section class="content-header">
	<div class="container-fluid" id="trial_to_register">
    @if($select_calendar_id > 0)
      {{-- ２．体験授業を選択し、予定設定のテンプレートとして利用 --}}
      <form method="POST"  action="/{{$domain}}/{{$item->id}}/to_calendar_setting">
        @csrf
        <input type="hidden" name="calendar_id" value="{{$select_calendar_id}}">
        <input type="hidden" name="teacher_id" value="{{$candidate_teacher->id}}">
        <div class="row mb-1">
          <div class="col-md-4">
            <div class="card card-widget mb-2">
              <div class="card-header">
                <i class="fa fa-user-tie mr-1"></i>{{__('labels.charge_teacher')}}
              </div>
              <div class="card-footer">
                @component('trials.forms.charge_teacher', ['teacher' => $candidate_teacher,  'attributes' => $attributes, 'user' => $user,'is_detail'=>true])
                  @slot('addon')
                  {{--
                  <div class="col-12">
                  @component('components.calendar', [
                    'id' => 1,
                    'mode'=>'week',
                    'user_id' => $candidate_teacher->user_id, 'teacher_id' => $candidate_teacher->id])
                    @slot('event_select')
                    @endslot
                    @slot('event_click')
                    eventClick: function(event, jsEvent, view) {
                      if(event.status == "trial") return false;
                      $calendar.fullCalendar('unselect');
                      base.showPage('dialog', "subDialog", "{{__('labels.schedule_details')}}", "/calendars/"+event.id);
                    },
                    @endslot
                    @slot('event_render')
                    eventRender: function(event, element) {
                      var title = '{{__('labels.schedule_add')}}';
                      if(event['student_name']){
                        title = event['student_name']+'('+event['subject']+')<br>'+event['start_hour_minute']+'-'+event['end_hour_minute'];
                      }
                      event_render(event, element, title);
                    },
                    @endslot
                  @endcomponent
                  </div>
                  --}}
                  @endslot
                @endcomponent
              </div>
            </div>
            @component('trials.forms.trial_week_time',['teacher' => $candidate_teacher, 'item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
          </div>
          <div class="col-md-8">
            <div class="card card-widget mb-2">
              <div class="card-header">
                <i class="fa fa-edit mr-1"></i>{{__('labels.regular_schedule_setting')}}
              </div>
              <div class="card-footer">
                <div class="row">
                  @component('calendar_settings.forms.course_type', ['item'=>$item,'select_lesson' => $select_lesson,  'attributes' => $attributes]) @endcomponent
                  @component('calendar_settings.forms.charge_subject', ['item'=>$item, 'select_lesson' => $select_lesson, 'candidate_teacher' => $candidate_teacher, 'attributes' => $attributes, 'calendar'=>$calendar]) @endcomponent
                  @component('calendar_settings.forms.lesson_place_floor', ['item'=>$item, 'attributes' => $attributes, 'calendar'=>$calendar]) @endcomponent
                  {{--
                  @component('calendar_settings.forms.lesson_week', ['item'=>$item, 'teacher'=> $candidate_teacher ,'attributes' => $attributes, 'calendar'=>$calendar]) @endcomponent
                  @component('calendar_settings.forms.select_time', ['item'=>$item, 'attributes' => $attributes, 'calendar'=>$calendar]) @endcomponent
                  @component('students.forms.course_minutes', ['_teacher'=>true, '_edit'=>false, 'item'=> $item->trial_students->first()->student->user, 'attributes' => $attributes, 'calendar'=>$calendar]) @endcomponent
                  --}}
                  @component('trials.forms.lesson_week', ['item'=>$item, 'teacher'=> $candidate_teacher ,'attributes' => $attributes, 'calendar'=>$calendar]) @endcomponent
                  <input type="hidden" name="course_minutes" value="{{$item->get_tag('course_minutes')->tag_value}}">

                </div>
                <div class="row">
                  <div class="col-6 mb-1">
                    <button type="button" class="btn btn-submit btn-primary btn-block">
                      <i class="fa fa-check mr-1"></i>
                      {{__('labels.regular_schedule_setting')}}
                    </button>
                  </div>
                  <div class="col-6 mb-1">
                    <a href="/{{$domain}}/{{$item->id}}" role="button" class="btn btn-secondary btn-block float-left mr-1">
                      <i class="fa fa-arrow-circle-left mr-1"></i>
                      {{__('labels.cancel_button')}}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    @else
    {{-- １．体験授業を選択し、予定設定を作成する --}}
    <div class="row">
      <div class="col-md-12">
        <div class="card card-widget mb-2">
          <div class="card-header">
            <i class="fa fa-clock mr-1"></i>{{__('labels.regular_schedule_setting')}}
          </div>
          <div class="card-footer">
            @component('trials.forms.user_calendar_setting',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="card card-widget mb-2">
          <div class="card-header">
            <i class="fa fa-envelope-open-text mr-1"></i>{{__('labels.trials_schedule_history')}}
          </div>
          <div class="card-footer">
            @component('trials.forms.trial_calendar',['is_register' => true, 'item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
</section>
<script>
$(function(){
  base.pageSettinged("trial_to_register", null);
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('trial_to_register')){
      var from_time_slot = $('input[name="from_time_slot"]').val();
      var to_time_slot = $('input[name="to_time_slot"]').val();
      var lesson_week = $('input[name="lesson_week"]').val();
      if(util.isEmpty(from_time_slot) || util.isEmpty(to_time_slot) || util.isEmpty(lesson_week)){
        front.showValidateError('button.btn.btn-submit', '予定が選択されていません');
        return;
      }
      $("form").submit();
    }
  });
});
</script>
@endsection
