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
      {{-- TODO 修正lesson_week_count --}}
      <form method="POST"  action="/{{$domain}}/{{$item->id}}/to_calendar_setting">
        @csrf
        <input type="text" name="dummy" style="display:none;" / >
        <input type="hidden" name="calendar_id" value="{{$select_calendar_id}}">
        <input type="hidden" name="teacher_id" value="{{$candidate_teacher->id}}">
        <input type="hidden" name="course_minutes" value="{{$item->get_tag('course_minutes')->tag_value}}">
        <input type="hidden" name="lesson_week_count" value="{{$item->student->user->get_enable_calendar_setting_count()}}">
        <div class="row mb-1">
          <div class="col-md-12">
            <div class="card card-widget mb-2">
              <div class="card-header">
                <i class="fa fa-edit mr-1"></i>{{__('labels.regular_schedule_setting')}}
              </div>
              <div class="card-footer">
                <div class="row">
                  <div class="col-12 mt-2">
                    <div class="form-group">
                      <label for="course_type" class="w-100">
                        {{__('labels.charge_teacher')}}
                      </label>
                      <span>
                        {{$candidate_teacher->name()}}
                      </span>
                    </div>
                  </div>
                  @component('calendar_settings.forms.charge_subject', ['item'=>$item, 'select_lesson' => $select_lesson, 'candidate_teacher' => $candidate_teacher, 'attributes' => $attributes, 'calendar'=>$calendar]) @endcomponent
                  @component('calendar_settings.forms.lesson_place_floor', ['item'=>$item, 'attributes' => $attributes, 'calendar'=>$calendar]) @endcomponent
                  @component('calendar_settings.forms.course_type', ['item'=>$item,'select_lesson' => $select_lesson,  'attributes' => $attributes]) @endcomponent
                  @component('calendars.forms.add_type', ['item'=>$item,]) @endcomponent
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12">
            <div class="card card-widget mb-2">
              <div class="card-header">
                <i class="fa fa-calendar-alrt mr-1"></i>
                {{__('labels.regular_schedule_setting')}}
              </div>
              <div class="card-footer">
                <div class="row">
                  @component('trials.forms.lesson_week', ['item'=>$item, 'teacher'=> $candidate_teacher ,'attributes' => $attributes, 'calendar'=>$calendar]) @endcomponent
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
          @component('trials.forms.select_teacher', ['item'=>$item,'select_lesson' => $select_lesson,'domain'=>$domain,'domain_name'=>$domain_name,'user'=>$user, 'candidate_teachers'=>[], 'attributes' => $attributes, 'is_calendar_setting' => true]) @endcomponent
          {{--
          <div class="card-header">
            <i class="fa fa-envelope-open-text mr-1"></i>{{__('labels.trials_schedule_history')}}
          </div>
          <div class="card-footer">
            @component('trials.forms.trial_calendar',['is_register' => true, 'item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
          </div>
          --}}
        </div>
      </div>
    </div>
    @endif
  </div>
</section>
@component('tuitions.forms.calc_script', []) @endcomponent

<script>
$(function(){
  base.pageSettinged("trial_to_register", null);
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('trial_to_register') && lesson_week_datetime_validate()){
      var from_time_slot = $('input[name="from_time_slot"]').val();
      var to_time_slot = $('input[name="to_time_slot"]').val();
      var calendar_setting_id = $('input[name="calendar_setting_id"]').val();
      var lesson_week = $('input[name="lesson_week"]').val();
      if(util.isEmpty(calendar_setting_id)){
        if(util.isEmpty(from_time_slot) || util.isEmpty(to_time_slot) || util.isEmpty(lesson_week)){
          front.showValidateError('button.btn.btn-submit', '予定が選択されていません');
          return;
        }
      }
      $("form").submit();
    }
  });
});
</script>
@endsection
