@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')
@include('dashboard.widget.milestones')

@section('contents')
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
        @component('components.calendar', ['user_id' => $item->user_id, 'student_id' => $item->id, 'domain' => $domain])
          @slot('event_select')
          @endslot
          @slot('event_click')
          eventClick: function(event, jsEvent, view) {
            $calendar.fullCalendar('unselect');
            switch(event.own_member.status){
              case "confirm":
                base.showPage('dialog', "subDialog", "予定確認", "/calendars/"+event.id+"/status_update/fix?student_id={{$item->id}}");
                console.log("/calendars/"+event.id+"/status_update/fix?student_id={{$item->id}}");
                break;
              case "fix":
                base.showPage('dialog', "subDialog", "お休み連絡", "/calendars/"+event.id+"/status_update/rest?student_id={{$item->id}}");
                console.log("/calendars/"+event.id+"/status_update/rest?student_id={{$item->id}}");
                break;
              case "new":
              case "rest":
              case "cancel":
              case "absence":
              case "presence":
              case "exchange":
              default:
                base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id+"?student_id={{$item->id}}");
                console.log("/calendars/"+event.id+"?student_id={{$item->id}}");
                break;
            }
          },
          @endslot
          @slot('event_render')
          eventRender: function(event, element, view) {
            var title = '授業追加';
            console.log(event);
            title = event['teacher_name']+'('+event['subject'].join('/')+')<br>'+event['start_hour_minute']+'-'+event['end_hour_minute'];
            event_render(event, element, title);
          },
          @endslot
        @endcomponent
			</div>

		</div>
	</div>
</section>

@endsection
