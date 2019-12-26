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
            var _course_minutes = end.diff(start, 'minutes');
            $calendar.fullCalendar("removeEvents", -1);
            $calendar.fullCalendar('unselect');
            $calendar.fullCalendar('addEventSource', [{
              id:-1,
              title: "勤務追加",
              start: start,
              end : end,
              status : "new",
            }]);
            var start_date = util.format("{0}/{1}/{2}", start.year(), (start.month()+1) , start.date());
            var end_date = util.format("{0}/{1}/{2}", end.year(), (end.month()+1) , end.date());
            var param ="";
            param += "?manager_id={{$item->id}}";
            param += "&start_date="+start_date;
            param += "&start_hours="+start.hour();
            param += "&start_minutes="+start.minute();
            param += "&end_date="+end_date;
            param += "&end_hours="+end.hour();
            param += "&end_minutes="+end.minute();
            param += "&course_minutes="+_course_minutes;
            base.showPage('dialog', "subDialog", "勤務追加", "/calendars/create"+param, function(){
              $calendar.fullCalendar("removeEvents", -1);
            });
          },
          @endslot
          @slot('event_click')
          eventClick: function(event, jsEvent, view) {
            $calendar.fullCalendar('unselect');
						if(event.work==9){
	            switch(event.total_status){
	              case "new":
	                base.showPage('dialog', "subDialog", "予定を確定する", "/calendars/"+event.id+"/status_update/confirm");
	                break;
	              case "confirm":
	                break;
	              case "fix":
	                if(event.is_passed==true){
	                  base.showPage('dialog', "subDialog", "勤怠をつける", "/calendars/"+event.id+"/status_update/presence");
	                }
	                else{
	                  base.showPage('dialog', "subDialog", "休み連絡", "/calendars/"+event.id+"/status_update/lecture_cancel");
	                }
	                break;
	              case "rest":
	              case "cancel":
	              case "absence":
	              case "presence":
	              case "exchange":
	              default:
	                base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id);
	                break;
	            }
						}
						else {
							base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id);
						}
          },
          @endslot
        @endcomponent
			</div>
		</div>
	</div>
</section>
@endsection
