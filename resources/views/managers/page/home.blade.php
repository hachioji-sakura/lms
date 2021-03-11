@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends($domain.'.page')
@include($domain.'.menu')

@section('sub_contents')
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
      </div>
      <div class="row">
        <div class="col-12 col-lg-4 col-md-6 mb-1">
          <a href="/trials?list=new">
          <div class="info-box">
            <span class="info-box-icon bg-danger">
              <i class="fa fa-archway"></i>
            </span>
            <div class="info-box-content text-dark">
              <b class="info-box-text text-lg">体験申し込み</b>
              <span class="text-sm">体験申し込みの管理</span>
            </div>
          </div>
          </a>
        </div>
        <div class="col-12 col-lg-4 col-md-6 mb-1">
          <a href="/events">
          <div class="info-box">
            <span class="info-box-icon bg-danger">
              <i class="fa fa-envelope-open-text"></i>
            </span>
            <div class="info-box-content text-dark">
              <b class="info-box-text text-lg">イベント一覧</b>
              <span class="text-sm">講習・模試等イベントの管理</span>
            </div>
          </div>
          </a>
        </div>
        <div class="col-12 col-lg-4 col-md-6 mb-1">
          <a href="/calendars">
          <div class="info-box">
            <span class="info-box-icon bg-info">
              <i class="fa fa-clock"></i>
            </span>
            <div class="info-box-content text-dark">
              <b class="info-box-text text-lg">{{__('labels.schedule_list')}}</b>
              <span class="text-sm">予定検索</span>
            </div>
          </div>
          </a>
        </div>
        <div class="col-12 col-lg-4 col-md-6 mb-1">
          <a href="/calendar_settings">
          <div class="info-box">
            <span class="info-box-icon bg-info">
              <i class="fa fa-calendar-alt"></i>
            </span>
            <div class="info-box-content text-dark">
              <b class="info-box-text text-lg">{{__('labels.repeat_schedule_settings_list')}}</b>
              <span class="text-sm">繰返予定の検索</span>
            </div>
          </div>
          </a>
        </div>
        <div class="col-12 col-lg-4 col-md-6 mb-1">
          <a href="/curriculums">
          <div class="info-box">
            <span class="info-box-icon bg-info">
              <i class="fa fa-sitemap"></i>
            </span>
            <div class="info-box-content text-dark">
              <b class="info-box-text text-lg">単元管理</b>
              <span class="text-sm">単元の登録、編集、削除</span>
            </div>
          </div>
          </a>
        </div>
        <div class="col-12 col-lg-4 col-md-6 mb-1">
          <a href="/text_materials">
          <div class="info-box">
            <span class="info-box-icon bg-info">
              <i class="fa fa-book"></i>
            </span>
            <div class="info-box-content text-dark">
              <b class="info-box-text text-lg">教材管理</b>
              <span class="text-sm">教材の登録、編集、削除</span>
            </div>
          </div>
          </a>
        </div>
      </div>
  	</div>
  </section>
  @endif
@endsection
