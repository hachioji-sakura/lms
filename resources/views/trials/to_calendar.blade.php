@section('title')
  {{$domain_name}}詳細
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section class="content-header">
	<div class="container-fluid" id="trial_to_calendar">
    @if($select_teacher_id > 0)
    <form method="POST"  action="/{{$domain}}/{{$item->id}}/confirm">
      @csrf
      @method('PUT')
      <input type="hidden" name="teacher_id" value="{{$candidate_teachers[0]->id}}">
      <input type="hidden" name="lesson" value="{{$select_lesson}}">
      <input type="hidden" name="start_time" value="">
      <input type="hidden" name="end_time" value="">
      <div class="row mb-1">
        <div class="col-6">
          <div class="card card-widget mb-2">
            <div class="card-header">
              <i class="fa fa-user-tie mr-1"></i>担当講師
            </div>
            <div class="card-footer">
              @component('trials.forms.charge_teacher', ['teacher' => $candidate_teachers[0], 'attributes' => $attributes, 'user' => $user,])
                @slot('addon')
                @endslot
              @endcomponent
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="card card-widget mb-2">
            <div class="card-header">
              <i class="fa fa-edit mr-1"></i>授業設定
            </div>
            <div class="card-footer">
              <div class="row">
                @component('calendar_settings.forms.course_type', ['item'=>$item, 'attributes' => $attributes]) @endcomponent
                @component('calendar_settings.forms.charge_subject', ['item'=>$item, 'select_lesson' => $select_lesson, 'candidate_teacher' => $candidate_teachers[0], 'attributes' => $attributes]) @endcomponent
                @component('calendar_settings.forms.lesson_place_floor', ['item'=>$item, 'attributes' => $attributes]) @endcomponent
                @component('trials.forms.matching_decide', ['attributes' => $attributes]) @endcomponent
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-1">
        @component('trials.forms.select_trial_date', ['item'=>$item, 'candidate_teacher' => $candidate_teachers[0], 'attributes' => $attributes]) @endcomponent
      </div>
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
      @include('trials.forms.select_teacher')
      @yield('select_teacher_form')
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
      $("form").submit();
    }
  });

});
</script>
@endsection
