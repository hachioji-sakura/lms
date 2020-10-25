@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends($domain.'.page')
@include($domain.'.menu')

@section('sub_contents')
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
@endsection
