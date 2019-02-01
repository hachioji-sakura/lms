@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')
@include('dashboard.widget.milestones')

@section('contents')
<section id="member" class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4">
        @component('components.profile', ['item' => $item, 'user' => $user, 'use_icons' => $use_icons, 'domain' => $domain, 'domain_name' => $domain_name])
            @slot('courtesy')
            　様
            @endslot
            @slot('alias')
              <h6 class="widget-user-desc">
                @foreach($item["tags"] as $tag)
                <small class="badge badge-secondary mt-1 mr-1">
                  {{$tag->name()}}
                </small>
                @endforeach
              </h6>
              <div class="card-footer p-0">
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a href="/examinations" class="nav-link">
                      <i class="fa fa-file-signature mr-2"></i>
                      確認テスト
                      <span class="float-right badge bg-danger">New</span>
                    </a>
                  </li>
                </ul>
              </div>
            @endslot
        @endcomponent
			</div>
			<div class="col-md-8">
        @yield('milestones')
			</div>
		</div>
	</div>
</section>

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
            switch(event.status){
              case "confirm":
                base.showPage('dialog', "subDialog", "予定確認", "/calendars/"+event.id+"/confirm");
                break;
              case "fix":
                base.showPage('dialog', "subDialog", "欠席連絡", "/calendars/"+event.id+"/rest?_page_origin={{$domain}}_{{$item->id}}_calendar&student_id={{$item->id}}");
                break;
              case "new":
              case "rest":
              case "cancel":
              case "absence":
              case "presence":
              case "exchange":
              default:
                base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id+"?_page_origin={{$domain}}_{{$item->id}}_calendar&student_id={{$item->id}}");
                break;
            }
          },
          @endslot
          @slot('event_render')
          eventRender: function(event, element, view) {
            var title = '授業追加';
            if(event['student_name']){
              title = event['teacher_name']+'('+event['subject']+')<br>'+event['start_hour_minute']+'-'+event['end_hour_minute'];
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
