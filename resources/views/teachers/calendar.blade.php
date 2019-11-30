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
        @component('components.calendar', ['user_id' => $item->user_id, 'teacher_id' => $item->id, 'domain' => $domain])
          @slot('event_select')
          // 選択可
          selectable: true,
          select: function(start, end, jsEvent, view , resource){
            var _course_minutes = end.diff(start, 'minutes');
            $calendar.fullCalendar("removeEvents", -1);
            $calendar.fullCalendar('unselect');
            $calendar.fullCalendar('addEventSource', [{
              id:-1,
              title: "{{__('labels.schedule_add')}}",
              start: start,
              end : end,
              status : "new",
              teaching_code : "add",
              selected : true,
            }]);

            var start_date = util.format("{0}/{1}/{2}", start.year(), (start.month()+1) , start.date());
            var end_date = util.format("{0}/{1}/{2}", end.year(), (end.month()+1) , end.date());
            var param ="";
            param += "?teacher_id={{$item->id}}";
            param += "&start_date="+start_date;
            param += "&start_hours="+start.hour();
            param += "&start_minutes="+start.minute();
            param += "&end_date="+end_date;
            param += "&end_hours="+end.hour();
            param += "&end_minutes="+end.minute();
            param += "&course_minutes="+_course_minutes;
            console.log(param);
            base.showPage('dialog', "subDialog", "{{__('labels.schedule_add')}}", "/calendars/create"+param, function(){
              $calendar.fullCalendar("removeEvents", -1);
            });
          },
          @endslot
          @slot('event_click')
          eventClick: function(event, jsEvent, view) {
            $calendar.fullCalendar('unselect');
            switch(event.total_status){
              case "new":
                base.showPage('dialog', "subDialog", "{{__('labels.schedule_remind')}}", "/calendars/"+event.id+"/status_update/confirm");
                break;
              case "confirm":
                base.showPage('dialog', "subDialog", "{{__('labels.schedule_remind')}}", "/calendars/"+event.id+"/status_update/remind");
                break;
              case "fix":
                if(event.is_passed==true){
                  base.showPage('dialog', "subDialog", "{{__('labels.schedule_presence')}}", "/calendars/"+event.id+"/status_update/presence");
                }
                else{
                  base.showPage('dialog', "subDialog", "{{__('labels.ask_lecture_cancel')}}", "/calendars/"+event.id+"/status_update/lecture_cancel");
                }
                break;
              case "rest":
              case "cancel":
              case "absence":
              case "presence":
              case "exchange":
              default:
                base.showPage('dialog', "subDialog", "{{__('labels.schedule_details')}}", "/calendars/"+event.id);
                break;
            }
          },
          @endslot
          @slot('event_render')
          eventRender: function(event, element) {
            var title = "{{__('labels.schedule_add')}}";
            if(event['student_name']){
              title = event['student_name']+'('+event['place_floor_name']+')<br>'+event['start_hour_minute']+'-'+event['end_hour_minute'];
            }
            if(event['teaching_code']==""){
              title = event['work_name']+'('+event['place_floor_name']+')<br>'+event['start_hour_minute']+'-'+event['end_hour_minute'];
            }
            console.log(event);
            event_render(event, element, title, true);
          },
          @endslot
        @endcomponent
			</div>
		</div>
	</div>
</section>

@endsection
