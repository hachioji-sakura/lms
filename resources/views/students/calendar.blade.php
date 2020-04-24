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
        @component('components.calendar', ['user' => $user, 'teacher_id' => $item->id, 'domain' => $domain, 'filter'=>$filter, 'item'=>$item, 'attributes' => $attributes])
          @slot('event_select')
          @endslot
          @slot('event_click')
          eventClick: function(event, jsEvent, view) {
            $calendar.fullCalendar('unselect');
            @if($user->role!='student')
            switch(event.own_member.status){
              case "confirm":
                base.showPage('dialog', "subDialog", "予定確認", "/calendars/"+event.id+"/status_update/fix?student_id={{$item->id}}");
                console.log("/calendars/"+event.id+"/status_update/fix?student_id={{$item->id}}");
                break;
              case "fix":
                base.showPage('dialog', "subDialog", "お休み連絡", "/calendars/"+event.id+"/status_update/rest?student_id={{$item->id}}");
                console.log("/calendars/"+event.id+"/status_update/rest?student_id={{$item->id}}");
                break;
              case "rest":
                if(event.is_passed==false){
                  base.showPage('dialog', "subDialog", "休み取り消し連絡", "/calendars/"+event.id+"/status_update/rest_cancel?student_id={{$item->id}}");
                  console.log("/calendars/"+event.id+"/status_update/rest_cancel?student_id={{$item->id}}");
                  break;
                }
              case "new":
              case "cancel":
              case "absence":
              case "presence":
              case "exchange":
              default:
                base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id+"?student_id={{$item->id}}");
                console.log("/calendars/"+event.id+"?student_id={{$item->id}}");
                break;
            }
            @else
            base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id+"?student_id={{$item->id}}");
            @endif
          },
          @endslot
        @endcomponent
			</div>

		</div>
	</div>
</section>
@endsection
