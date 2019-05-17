@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

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
        @component('components.profile', ['item' => $item, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
            @slot('courtesy')
            @endslot
            @slot('alias')
              <h6 class="widget-user-desc">
                @foreach($item->user->tags as $tag)
                  @if($tag->tag_key=="teacher_no")
                    <small class="badge badge-dark mt-1 mr-1">
                      {{$tag->keyname()}}{{$tag->name()}}
                    </small>
                  @endif
                  @if($tag->tag_key=="lesson")
                    <small class="badge badge-primary mt-1 mr-1">
                      {{$tag->name()}}
                    </small>
                  @endif
                  @if($user->role==="manager" && $tag->tag_key=="teacher_character")
                    <small class="badge badge-info mt-1 mr-1">
                      {{$tag->name()}}
                    </small>
                  @endif
                @endforeach
              </h6>
            @endslot
        @endcomponent
			</div>
			<div class="col-md-8">
			</div>
		</div>
	</div>
</section>
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/{{$domain}}/{{$item->id}}/calendar" class="">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fa fa-calendar"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">カレンダー</b>
            <span class="text-sm">授業予定をカレンダー表示</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/{{$domain}}/{{$item->id}}/schedule" class="">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fa fa-clock"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">授業予定一覧</b>
            <span class="text-sm">授業予定をリスト表示</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a class="" href="javascript:void(0);"  page_form="dialog" page_url="/calendars/create?origin={{$domain}}&item_id={{$item->id}}" page_title="授業追加">
        <div class="info-box">
          <span class="info-box-icon bg-primary">
            <i class="fa fa-plus"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">授業追加</b>
            <span class="text-sm">授業の追加・振替</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/{{$domain}}/{{$item->id}}/month_work" class="">
        <div class="info-box">
          <span class="info-box-icon bg-success">
            <i class="fa fa-tasks"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">勤務実績</b>
            <span class="text-sm">月末の実績登録</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/{{$domain}}/{{$item->id}}/calendar_settings" class="">
        <div class="info-box">
          <span class="info-box-icon bg-success">
            <i class="fa fa-chalkboard-teacher"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">通常授業設定</b>
            <span class="text-sm">繰り返し授業予定の登録</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a class="" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="{{$domain_name}}設定">
        <div class="info-box">
          <span class="info-box-icon bg-secondary">
            <i class="fa fa-user-cog"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">講師設定</b>
            <span class="text-sm">担当科目、授業可能曜日の設定</span>
          </div>
        </div>
        </a>
      </div>
    </div>
	</div>
</section>

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
        <div class="card-body table-responsive p-0">
          @if(count($charge_students) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($charge_students as $student)
            <li class="col-lg-3 col-md-4 col-12" accesskey="" target="">
              <div class="row">
                <div class="col-6 text-center">
                  <a href="/students/{{$student->id}}">
                    <img src="{{$student->user->details()->icon}}" class="img-circle mw-64px w-50">
                    <br>
                    <ruby style="ruby-overhang: none">
                      <rb>{{$student->name()}}</rb>
                      <rt>{{$student->kana()}}</rt>
                    </ruby>
                  </a>
                </div>
                <div class="col-6 text-sm">
                  @if(!empty($student->current_calendar))
                      <i class="fa fa-calendar mr-1"></i>{{$student->current_calendar->date}}
                      <br>
                      <i class="fa fa-clock mr-1"></i>{{$student->current_calendar->timezone}}
                      <br>
                      @foreach($student->current_calendar['subject'] as $subject)
                      <span class="text-xs mx-2">
                        <small class="badge badge-primary mt-1 mr-1">
                          {{$subject}}
                        </small>
                      </span>
                      @endforeach
                  @else
                  -
                  @endif
                </div>
                <div class="col-12 text-sm">
                  @if(isset($student->current_calendar))
                    @if($user->role==="teacher" && $student->current_calendar->status==="fix" && date('Ymd', strtotime($student->current_calendar->start_time)) === date('Ymd'))
                      <a title="{{$student->current_calendar->id}}" href="javascript:void(0);" page_title="出欠を取る" page_form="dialog" page_url="/calendars/{{$student->current_calendar->id}}/presence?origin={{$domain}}&item_id={{$item->id}}&student_id={{$student->id}}" role="button" class="btn btn-info btn-sm w-100 mt-1">
                        <i class="fa fa-user-check mr-1"></i>
                        {{$student->current_calendar->status_name}}
                      </a>
                    @elseif($student->current_calendar->status==="confirm")
                    {{-- @elseif($student->current_calendar->status==="fix" || $student->current_calendar->status==="confirm") --}}
                      {{-- 予定確認 --}}
                      <a title="{{$student->current_calendar->id}}" href="javascript:void(0);" page_title="授業予定連絡" page_form="dialog" page_url="/calendars/{{$student->current_calendar->id}}/remind" role="button" class="btn btn-warning btn-sm w-100 mt-1">
                        <i class="fa fa-envelope mr-1"></i>
                        {{$student->current_calendar->status_name}}
                      </a>
                    @elseif($student->current_calendar->status==="presence")
                      {{-- 出席済み --}}
                      <a title="{{$student->current_calendar->id}}" href="javascript:void(0);" page_title="予定詳細" page_form="dialog" page_url="/calendars/{{$student->current_calendar->id}}" role="button" class="btn btn-success btn-sm w-100 mt-1">
                        <i class="fa fa-check-circle mr-1"></i>
                        {{$student->current_calendar->status_name}}
                      </a>
                    @elseif($student->current_calendar->status==="new")
                      {{-- 予定下書き --}}
                      <a title="{{$student->current_calendar->id}}" href="javascript:void(0);" page_title="予定を確定する" page_form="dialog" page_url="/calendars/{{$student->current_calendar->id}}/confirm" role="button" class="btn btn-secondary btn-sm w-100 mt-1">
                        <i class="fa fa-calendar-check mr-1"></i>
                        {{$student->current_calendar->status_name}}
                      </a>
                    @else
                      <a title="{{$student->current_calendar->id}}" href="javascript:void(0);" page_title="予定詳細" page_form="dialog" page_url="/calendars/{{$student->current_calendar->id}}" role="button" class="btn btn-secondary btn-sm w-100 mt-1">
                        <i class="fa fa-file-alt mr-1"></i>
                        {{$student->current_calendar->status_name}}
                      </a>
                    @endif
                  @endif
                </div>
            </li>
            @endforeach
          </ul>
          @else
          <div class="alert">
            <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
          </div>
          @endif
        </div>
      {{--
        <!-- /.card-body -->
        <div class="card-footer clearfix">
          <button type="button" class="btn btn-info btn-sm float-left">
            <i class="fa fa-plus mr-2"></i>追加
          </button>
        </div>
        --}}
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
