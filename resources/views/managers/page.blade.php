@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')

@include('dashboard.widget.comments')
@include($domain.'.menu')

{{--まだ対応しない
@include('dashboard.widget.milestones')
@include('dashboard.widget.events')
@include('dashboard.widget.tasks')
--}}

@section('contents')
<section id="member" class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
        @component('components.profile', ['item' => $item, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
            @slot('courtesy')
            @endslot
            @slot('alias')
            <h6 class="widget-user-desc">
              @foreach($item->user->tags as $tag)
                @if($tag->tag_key=="manager_no")
                  <small class="badge badge-dark mt-1 mr-1">
                    No.{{$tag->name()}}
                  </small>
                @endif
                @if($tag->tag_key=="manager_type" && $tag->tag_value!='disabled')
                  <small class="badge badge-primary mt-1 mr-1">
                    {{$tag->name()}}
                  </small>
                @endif
              @endforeach
            </h6>
            @endslot
        @endcomponent
			</div>
		</div>
	</div>
</section>
@if($user->role=="manager")
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/parents">
        <div class="info-box">
          <span class="info-box-icon bg-success">
            <i class="fa fa-user-friends"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">契約者一覧</b>
            <span class="text-sm">契約者（生徒保護者）管理</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/students">
        <div class="info-box">
          <span class="info-box-icon bg-success">
            <i class="fa fa-user-graduate"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">生徒一覧</b>
            <span class="text-sm">生徒の管理</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/managers">
        <div class="info-box">
          <span class="info-box-icon bg-success">
            <i class="fa fa-address-card"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">事務一覧</b>
            <span class="text-sm">事務員の登録</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/teachers">
        <div class="info-box">
          <span class="info-box-icon bg-success">
            <i class="fa fa-user-tie"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">講師一覧</b>
            <span class="text-sm">講師の登録</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/trials">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fa fa-envelope-open-text"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">体験申し込み</b>
            <span class="text-sm">体験申し込みの管理</span>
          </div>
        </div>
        </a>
      </div>
      {{--
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/comments">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fa fa-comments"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">コメント一覧</b>
            <span class="text-sm">コメントの管理</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/milestones">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fa fa-flag"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">目標一覧</b>
            <span class="text-sm">生徒目標の管理</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/events">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fa fa-calendar-check"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">イベント一覧</b>
            <span class="text-sm">イベントの管理</span>
          </div>
        </div>
        </a>
      </div>
      --}}
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/attributes">
        <div class="info-box">
          <span class="info-box-icon bg-warning">
            <i class="fa fa-tags"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">属性一覧</b>
            <span class="text-sm">定義項目の追加・編集</span>
          </div>
        </div>
        </a>
      </div>
    </div>
	</div>
</section>
@endif

<section class="content-header">
	<div class="container-fluid">
		<div class="row">
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/{{$domain}}/{{$item->id}}/month_work">
        <div class="info-box">
          <span class="info-box-icon bg-secondary">
            <i class="fa fa-tasks"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">勤務実績</b>
            <span class="text-sm">当月の出勤簿</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/{{$domain}}/{{$item->id}}/calendar">
        <div class="info-box">
          <span class="info-box-icon bg-secondary">
            <i class="fa fa-calendar-alt"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">カレンダー</b>
            <span class="text-sm">出勤予定（カレンダー）</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="事務設定">
        <div class="info-box">
          <span class="info-box-icon bg-secondary">
            <i class="fa fa-user-edit"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">事務設定</b>
            <span class="text-sm">勤務可能曜日・時間帯の設定</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/{{$domain}}/{{$item->id}}/calendar_settings">
        <div class="info-box">
          <span class="info-box-icon bg-secondary">
            <i class="fa fa-user-clock"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">シフト一覧</b>
            <span class="text-sm">繰り返し勤務曜日・時間帯の設定</span>
          </div>
        </div>
        </a>
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
