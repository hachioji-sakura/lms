@section('title')
  {{__('labels.calendar_page')}}
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


@section('contents')
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
        @component('components.calendar', ['user' => $user, 'teacher_id' => $item->id, 'domain' => $domain, 'filter'=>$filter, 'item'=>$item, 'attributes' => $attributes])
          @slot('event_select')
          // 選択可
          selectable: true,
          select: function(start, end, jsEvent, view , resource){
            event_create(start, end, jsEvent, view , resource);
          },
          @endslot
          @slot('event_click')
          eventClick: function(event, jsEvent, view) {
            $calendar.fullCalendar('unselect');
            if(event.id<0){
              event_create(event.start, event.end, jsEvent, view);
              return false;
            }
            if(event.work==5){
              //演習の場合は操作不要
              base.showPage('dialog', "subDialog", "{{__('labels.schedule_details')}}", "/calendars/"+event.id);
            }
            else {
              switch(event.total_status){
                case "new":
                  base.showPage('dialog', "subDialog", "{{__('labels.schedule_remind')}}", "/calendars/"+event.id+"/status_update/confirm");
                  break;
                case "confirm":
                  //生徒へ再送
                  base.showPage('dialog', "subDialog", "{{__('labels.schedule_remind')}}", "/calendars/"+event.id+"/status_update/remind");
                  break;
                case "fix":
                  if(event.is_passed==true){
                    //過ぎていたら出欠
                    base.showPage('dialog', "subDialog", "{{__('labels.schedule_presence')}}", "/calendars/"+event.id+"/status_update/presence");
                  }
                  else{
                    //過ぎていないなら休み取り消し
                    base.showPage('dialog', "subDialog", "{{__('labels.ask_lecture_cancel')}}", "/calendars/"+event.id+"/status_update/lecture_cancel");
                  }
                  break;
                case "absence":
                case "presence":
                  base.showPage('dialog', "subDialog", "{{__('labels.calendar_button_attendance')}}{{__('labels.edit')}}", "/calendars/"+event.id+"/status_update/presence");
                  break;
                case "rest":
                case "cancel":
                default:
                  base.showPage('dialog', "subDialog", "{{__('labels.schedule_details')}}", "/calendars/"+event.id);
                  break;
              }
            }
          },
          @endslot
        @endcomponent
			</div>
		</div>
	</div>
</section>
@endsection
