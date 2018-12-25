@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')

@include('dashboard.widget.comments')
@include('dashboard.widget.milestones')
@include('students.calendar')

{{--まだ対応しない
@include('dashboard.widget.events')
@include('dashboard.widget.tasks')
--}}

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
                <small class="badge badge-secondary mt-1">
                  {{$item->age}}歳
                </small>
                <!--
                  <i class="fa fa-calendar mr-1"></i>yyyy/mm/dd
                  <br>
                  <small class="badge badge-info mt-1">
                    <i class="fa fa-user mr-1"></i>中学1年
                  </small>
                  <small class="badge badge-info mt-1">
                    <i class="fa fa-chalkboard-teacher mr-1"></i>XXコース
                  </small>
              -->
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
                  if(value['status']==='cancel'){
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
              base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id+"/cancel?_page_origin={{$domain}}_{{$item->user_id}}");
            }
          },
          @endslot
        @endcomponent
			</div>
			<div class="col-12 col-lg-6 col-md-6">
				@yield('events')
			</div>
		</div>
	</div>
</section>

{{--まだ対応しない
<section class="content">
	@yield('tasks')
</section>
--}}
@endsection



@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open">
      <a href="{{$domain}}/{{$item->id}}" class="nav-link">
      <i class="nav-icon fa fa-user"></i>
      <p>
        学習管理
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="footer_form" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="目標登録">
            <i class="fa fa-comment-dots nav-icon"></i>コメント登録
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="footer_form" page_url="/milestones/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="目標登録">
            <i class="fa fa-flag nav-icon"></i>目標登録
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item has-treeview menu-open">
      <a href="{{$domain}}/{{$item->id}}/calendar" class="nav-link">
      <i class="nav-icon fa fa-clock"></i>
      <p>
        予定管理
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/calendar">
            <i class="fa fa-calendar-check nav-icon"></i>カレンダー
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="footer_form" page_url="/calendars/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="目標登録">
            <i class="fa fa-times nav-icon"></i>授業追加
          </a>
        </li>
      </ul>
    </li>
</ul>
@endsection
@section('page_footer')
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="footer_form" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="コメント登録">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
<dt>
  <a class="btn btn-app" href="javascript:void(0);"  page_form="footer_form" page_url="/milestones/create?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="目標登録">
    <i class="fa fa-flag"></i>目標登録
  </a>
</dt>
{{-- まだ対応しない
  <dt>
    <a class="btn btn-app" href="javascript:void(0);" accesskey="task_add" disabled>
      <i class="fa fa-plus"></i>タスク登録
    </a>
  </dt>
--}}
@endsection
