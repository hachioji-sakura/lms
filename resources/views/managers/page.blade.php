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
			<div class="col-md-12">
        @component('components.profile', ['item' => $item, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
            @slot('courtesy')
            @endslot
            @slot('alias')
            @endslot
        @endcomponent
			</div>
		</div>
	</div>
</section>
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

{{--まだ対応しない
<section class="content">
	@yield('tasks')
</section>
--}}
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-user-graduate"></i>
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
          <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="講師設定">
            <i class="fa fa-user-edit nav-icon"></i>事務設定
          </a>
        </li>
        {{--
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="コメント登録">
            <i class="fa fa-comment-dots nav-icon"></i>コメント登録
          </a>
        </li>
        --}}
      </ul>
    </li>
</ul>
@endsection

@section('page_footer')
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create??origin={{$domain}}&item_id={{$item->id}}" page_title="コメント登録">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
@endsection
