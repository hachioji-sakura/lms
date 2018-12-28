@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
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

@if($mode==="list")
<section class="content mb-2">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-calendar mr-1"></i>
            授業予定
          </h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if(isset($calendars))
          <table class="table table-hover">
            <tbody>
              <tr>
                <th>日付</th>
                <th>時間</th>
                <th>講師</th>
                <th>内容</th>
              </tr>
              @foreach($calendars as $calendar)
              <tr>
                <td>{{$calendar["date"]}}</td>
                <td>
                  {{$calendar["start"]}}～{{$calendar["end"]}}
                </td>
                <td>{{$calendar["teacher_name"]}}</td>
                <td>
                  <small class="badge badge-secondary mt-1 mr-1">
                    {{$calendar["lesson"]}}
                  </small>
                  <small class="badge badge-secondary mt-1 mr-1">
                    {{$calendar["course"]}}
                  </small>
                  <small class="badge badge-primary mt-1 mr-1">
                    {{$calendar["subject"]}}
                  </small>
                </td>
                <td>
                  <button type="button" class="btn btn-danger btn-sm float-left">
                    <i class="fa fa-minus-circle mr-2"></i>欠席連絡
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          授業予定はありません
          @endif
        </div>
      </div>
      <!-- /.card -->
    </div>
  </div>
</section>
@else
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

		</div>
	</div>
</section>
@endif

@endsection




@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-user"></i>
      <p>
        <ruby style="ruby-overhang: none">
          <rb>{{$item->name}}</rb>
          <rt>{{$item->kana}}</rt>
        </ruby>
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/" >
            <i class="fa fa-home nav-icon"></i>HOME
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/calendar" >
            <i class="fa fa-calendar nav-icon"></i>授業予定
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/calendar?mode=list" >
            <i class="fa fa-calendar nav-icon"></i>欠席連絡
          </a>
        </li>
      </ul>
    </li>
</ul>
@endsection
@section('page_footer')
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/calendars/cancel?_page_origin={{$domain}}_{{$item->id}}&student_id={{$item->id}}" page_title="欠席連絡">
    <i class="fa fa-comment-dots"></i>欠席連絡
  </a>
</dt>

@endsection
