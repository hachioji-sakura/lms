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
	                  base.showPage('dialog', "subDialog", "休み連絡", "/calendars/"+event.id+"/status_update/rest");
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
