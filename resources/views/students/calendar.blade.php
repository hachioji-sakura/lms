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
        @component('components.calendar', ['user_id' => $item->user_id,'domain' => $domain])
          @slot('set_calendar')
          service.getAjax(false, '/api_calendars/{{$item->user_id}}/'+start_time+'/'+end_time, null,
            function(result, st, xhr) {
              if(result['status']===200){
                var events = [];
                $.each(result['data'], function(index, value) {
                  var _type = 'study';
                  if(value['status']==='cancel' || value['status']==='rest'){
                    _type = 'cancel';
                  }
                  else if(value['exchanged_calendar_id']>0){
                    _type = "exchange";
                  }
                  var title = value['teacher_name']+'('+value['subject']+')<br>'+value['start']+'-'+value['end'];
                  events.push({
                    // イベント情報をセット
                    id: value['id'],
                    title: title,
                    description: value['teacher_name'],
                    start: value['start_time'],
                    end: value['end_time'],
                    type : _type
                  });
                });
                callback(events);
              }
            },
            function(xhr, st, err) {
                alert("カレンダー取得エラー");
                messageCode = "error";
                messageParam= "validate/querycheck\n"+err.message+"\n"+xhr.responseText;
            }
          );
          @endslot
          @slot('event_click')
          eventClick: function(event, jsEvent, view) {
            $calendar.fullCalendar('unselect');
            if(event.type==="study"){
              base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id+"/rest?_page_origin={{$domain}}_{{$item->id}}_calendar&student_id={{$item->id}}");
            }
            else {
              base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id+"?_page_origin={{$domain}}_{{$item->id}}_calendar&student_id={{$item->id}}");
            }
          },
          @endslot
        @endcomponent
			</div>

		</div>
	</div>
</section>

@endsection
