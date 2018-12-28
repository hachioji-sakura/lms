@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')

@include('dashboard.widget.comments')

{{--まだ対応しない
@include('dashboard.widget.milestones')
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
{{--
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
                var i=0;
                $.each(result['data'], function(index, value) {
                  i++;
                  var _type = 'study';
                  if(value['status']==='cancel'){
                    _type = 'cancel';
                  }
                  else if(value['exchanged_calendar_id']>0){
                    _type = "exchange";
                  }
                  var title = value['student_name']+'('+value['subject']+')<br>'+value['start']+'-'+value['end'];
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
                console.log(i);
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
              base.showPage('dialog', "subDialog", "休暇申請", "/calendars/"+event.id+"/cancel?_page_origin={{$domain}}_{{$item->user_id}}");
            }
          },
          @endslot
        @endcomponent
			</div>
		</div>
	</div>
</section>
--}}
<section class="content mb-2">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-users mr-1"></i>
            担当生徒
          </h3>
          <div class="card-tools">
            @component('components.search_word', ['search_word' => $search_word])
            @endcomponent
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          <table class="table table-hover">
            <tbody>
              <tr>
                <th>ID</th>
                <th>名前</th>
                <th>次回レッスン</th>
                <th>-</th>
              </tr>
              @foreach($charge_students as $charge_student)
              <tr>
                <td>{{$charge_student->id}}</td>
                <td>
                  <a href="/students/{{$charge_student->id}}">
                  <ruby style="ruby-overhang: none">
                    <rb>{{$charge_student->name}}</rb>
                    <rt>{{$charge_student->kana}}</rt>
                  </ruby>
                  </a>
                </td>
                <td>
                  @if(!empty($charge_student->current_schedule))
                    <i class="fa fa-calendar mr-1"></i>
                    <a href="javascript:void(0);" page_title="予定詳細" page_form="dialog" page_url="/calendars/{{$charge_student->calendar_id}}">
                      {{$charge_student->current_schedule}}
                    </a>
                  @else
                  -
                  @endif
                </td>
                <td>
                  @if(!empty($charge_student->current_schedule))
                    <small class="badge badge-secondary mt-1 mr-1">
                      {{$charge_student->lesson}}
                    </small>
                    <small class="badge badge-secondary mt-1 mr-1">
                      {{$charge_student->course}}
                    </small>
                    <small class="badge badge-primary mt-1 mr-1">
                      {{$charge_student->subject}}
                    </small>
                  @else
                  -
                  @endif
                </td>
                {{--
                <td>
                  <button type="button" class="btn btn-danger btn-sm float-left">
                    <i class="fa fa-minus-circle mr-2"></i>外す
                  </button>
                </td>
                --}}
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
          <button type="button" class="btn btn-info btn-sm float-left">
            <i class="fa fa-plus mr-2"></i>追加
          </button>
        </div>
      </div>
      <!-- /.card -->
    </div>
  </div>
</section>

{{--まだ対応しない
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 col-lg-6 col-md-6">
				@yield('milestones')
			</div>
			<div class="col-12 col-lg-6 col-md-6">
				@yield('events')
			</div>
		</div>
	</div>
</section>

<section class="content">
	@yield('tasks')
</section>
--}}
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
      <ul class="nav nav-treeview hr-1 bd-light">
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
      </ul>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="目標登録">
            <i class="fa fa-comment-dots nav-icon"></i>コメント登録
          </a>
        </li>
        {{--
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/milestones/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="目標登録">
            <i class="fa fa-flag nav-icon"></i>目標登録
          </a>
        </li>
        --}}
      </ul>
    </li>
</ul>
@endsection

@section('page_footer')
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="コメント登録">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
{{-- まだ対応しない
  <dt>
    <a class="btn btn-app" href="javascript:void(0);"  page_form="footer_form" page_url="/milestones/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="目標登録">
      <i class="fa fa-flag"></i>目標登録
    </a>
  </dt>
--}}
@endsection
