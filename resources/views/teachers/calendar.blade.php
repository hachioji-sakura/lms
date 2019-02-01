@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


@section('contents')
{{--
<section id="member" class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4">
        @component('components.profile', ['item' => $item, 'user' => $user, 'use_icons' => $use_icons, 'domain' => $domain, 'domain_name' => $domain_name])
            @slot('courtesy')
            @endslot

            @slot('alias')
              <h6 class="widget-user-desc">
                @foreach($item["tags"] as $tag)
                <small class="badge badge-secondary mt-1 mr-1">
                  {{$tag->name()}}
                </small>
                @endforeach
              </h6>
            @endslot
        @endcomponent
			</div>
			<div class="col-md-8">
				@yield('comments')
			</div>
		</div>
	</div>
</section>
--}}
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
        @component('components.calendar', ['user_id' => $item->user_id, 'teacher_id' => $item->id, 'domain' => $domain])
          @slot('event_select')
          // 選択可
          selectable: true,
          select: function(start, end, jsEvent, view , resource){
            var _lesson_time = end.diff(start, 'minutes');
            $calendar.fullCalendar("removeEvents", -1);
            $calendar.fullCalendar('unselect');
            $calendar.fullCalendar('addEventSource', [{
              id:-1,
              title: "授業追加",
              start: start,
              end : end,
              status : "new",
            }]);
            var start_date = util.format("{0}/{1}/{2}", start.year(), (start.month()+1) , start.date());
            var param ="?_page_origin={{$domain}}_{{$item->id}}";
            param += "&teacher_id={{$item->id}}";
            param += "&start_date="+start_date;
            param += "&start_hours="+start.hour();
            param += "&start_minutes="+start.minute();
            param += "&lesson_time="+_lesson_time;
            base.showPage('dialog', "subDialog", "授業追加", "/calendars/create"+param, function(){
              $calendar.fullCalendar("removeEvents", -1);
            });
          },
          @endslot
          @slot('event_click')
          eventClick: function(event, jsEvent, view) {
            $calendar.fullCalendar('unselect');
            switch(event.status){
              case "new":
                base.showPage('dialog', "subDialog", "予定を確定する", "/calendars/"+event.id+"/confirm");
                break;
              case "fix":
                base.showPage('dialog', "subDialog", "出欠を取る", "/calendars/"+event.id+"/presence");
                break;
              case "confirm":
              case "rest":
              case "cancel":
              case "absence":
              case "presence":
              case "exchange":
              default:
                base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id);
                break;
            }
          },
          @endslot
          @slot('event_render')
          eventRender: function(event, element) {
            var title = '授業追加';
            if(event['student_name']){
              title = event['student_name']+'('+event['subject']+')<br>'+event['start_hour_minute']+'-'+event['end_hour_minute'];
            }
            event_render(event, element, title);
          },
          @endslot
        @endcomponent
			</div>
		</div>
	</div>
</section>

@endsection
