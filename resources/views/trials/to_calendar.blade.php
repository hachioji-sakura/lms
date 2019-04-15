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
