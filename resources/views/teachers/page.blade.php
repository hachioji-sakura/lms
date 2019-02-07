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
        @component('components.profile', ['item' => $item, 'user' => $user, 'use_icons' => $use_icons, 'domain' => $domain, 'domain_name' => $domain_name])
            @slot('courtesy')
            @endslot
            @slot('alias')
              <h6 class="widget-user-desc">
                {{--
                @foreach($item->user->tags as $tag)
                <small class="badge badge-secondary mt-1 mr-1">
                  {{$tag->keyname()}}{{$tag->name()}}
                </small>
                @endforeach
                --}}
              </h6>
            @endslot
        @endcomponent
			</div>
			<div class="col-md-8">
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
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if(count($charge_students) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($charge_students as $charge_student)
            <li class="col-lg-3 col-md-4 col-12" accesskey="" target="">
              <div class="row">
                <div class="col-6 text-center">
                  <a href="/students/{{$charge_student->student->id}}">
                    <img src="{{$charge_student->student->user->details()->icon}}" class="img-circle mw-64px w-50">
                    <br>
                    <ruby style="ruby-overhang: none">
                      <rb>{{$charge_student->student->name()}}</rb>
                      <rt>{{$charge_student->student->kana()}}</rt>
                    </ruby>
                  </a>
                </div>
                <div class="col-6 text-sm">
                  @if(!empty($charge_student->current_calendar()))
                      <i class="fa fa-calendar mr-1"></i>{{$charge_student->current_calendar()->details()->date}}
                      <br>
                      <i class="fa fa-clock mr-1"></i>{{$charge_student->current_calendar()->details()->timezone}}
                      <br>
                      <small class="badge badge-info mt-1 mr-1">
                        {{$charge_student->current_calendar()->details()->subject}}
                      </small>
                  @else
                  -
                  @endif
                </div>
                <div class="col-12 text-sm">
                  @if(!empty($charge_student->current_calendar()) && $user->role==="teacher")
                    @if($charge_student->current_calendar()->details()->status==="fix" && date('Ymd', strtotime($charge_student->current_calendar()->details()->start_time)) === date('Ymd'))
                      <a title="{{$charge_student->current_calendar()->details()->id}}" href="javascript:void(0);" page_title="出欠を取る" page_form="dialog" page_url="/calendars/{{$charge_student->current_calendar()->details()->id}}/presence?_page_origin={{$domain}}_{{$item->id}}&student_id={{$charge_student->student->id}}" role="button" class="btn btn-info btn-sm w-100 mt-1">
                        <i class="fa fa-user-check mr-1"></i>
                        {{$charge_student->current_calendar()->details()->status_name}}
                      </a>
                    @elseif($charge_student->current_calendar()->details()->status==="presence")
                      {{-- 出席済み --}}
                      <a title="{{$charge_student->calendar_id}}" href="javascript:void(0);" page_title="予定詳細" page_form="dialog" page_url="/calendars/{{$charge_student->calendar_id}}" role="button" class="btn btn-success btn-sm w-100 mt-1">
                        <i class="fa fa-check-circle mr-1"></i>
                        {{$charge_student->current_calendar()->details()->status_name}}
                      </a>
                    @else
                      <a title="{{$charge_student->current_calendar()->details()->id}}" href="javascript:void(0);" page_title="予定詳細" page_form="dialog" page_url="/calendars/{{$charge_student->current_calendar()->details()->id}}" role="button" class="btn btn-secondary btn-sm w-100 mt-1">
                        {{$charge_student->current_calendar()->details()->status_name}}
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
