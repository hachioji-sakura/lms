@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')
@section('contents')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title" id="charge_students">
          <i class="fa fa-calendar mr-1"></i>
          体験授業一覧
        </h3>
      </div>
      <div id="trial_list" class="card-body table-responsive p-3">
        @if(count($items) > 0)
          @foreach($items as $item)
            <ul class="timeline timeline-inverse">
                <li>
                  <i class="fa fa-envelope bg-secondary"></i>
                  <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock mx-1"></i>第1希望:{{$item['date1']}}</span>
                    <span class="time"><i class="fa fa-clock mx-1"></i>第2希望:{{$item['date2']}}</span>
                    <h3 class="timeline-header">
                      <i class="fa fa-user mx-1"></i>{{$item['student_name']}}
                      <i class="fa fa-school mx-1"></i>{{$item['grade']}}
                      <small class="badge badge-{{$item['status_style']}} mt-1 mr-1">
                        {{$item['status_name']}}
                      </small>
                      {{-- <a href="javascript:void(0);" title="{{$item["id"]}}" page_title="詳細" page_form="dialog" page_url="/trials/{{$item["id"]}}" role="button" class="">
                        --}}
                      <a href="/trials/{{$item["id"]}}" class="">
                        詳細
                      </a>
                    </h3>
                    <div class="timeline-body">
                      {{$item['remark']}}
                    </div>
                    <div class="timeline-footer">
                      @if($item["status"]==="new")
                      <a title="" href="javascript:void(0);" page_title="予定作成" page_form="dialog" page_url="/trials/{{$item['id']}}/confirm" role="button" class="btn btn-primary btn-sm w-100 mt-1">
                        <i class="fa fa-user-check mr-1"></i>
                        予定作成
                      </a>
                      @elseif($item["status"]==="confirm")
                      <a title="" href="javascript:void(0);" page_title="予定作成" page_form="dialog" page_url="/trials/{{$item['id']}}/remind" role="button" class="btn btn-default btn-sm w-100 mt-1">
                        <i class="fa fa-envelope mr-1"></i>
                        確認メール
                      </a>
                      @endif
                    </div>
                  </div>
                </li>
                @if(isset($item['calendar']))
                <li>
                  <i class="fa fa-calendar-check bg-success"></i>
                  <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock mx-1"></i>{{$item['calendar']['datetime']}}</span>
                    <h3 class="timeline-header">
                      <i class="fa fa-user-tie mx-1"></i>{{$item['calendar']['teacher_name']}}
                      <i class="fa fa-map-marked mx-1"></i>{{$item['calendar']['place']}}
                      <small class="badge badge-{{$item['calendar']->status_style()}} mt-1 mr-1">
                        {{$item['calendar']['status_name']}}
                      </small>
                      <a href="javascript:void(0);" title="{{$item["calendar"]["id"]}}" page_title="詳細" page_form="dialog" page_url="/calendars/{{$item["calendar"]["id"]}}" role="button" class="">
                        詳細
                      </a>
                    </h3>
                    <div class="timeline-body">
                      {{$item['calendar']['remark']}}
                    </div>
                    <div class="timeline-footer">
                      @if($item["status"]==="cancel" || $item["status"]==="rest")
                      {{--　TODO:cancel or restで、体験のフローは止まるので、何等か対策が必要
                      <a title="" href="javascript:void(0);" page_title="予定作成" page_form="dialog" page_url="/trials/{{$item['id']}}/remake" role="button" class="btn btn-primary btn-sm w-100 mt-1">
                        <i class="fa fa-user-check mr-1"></i>
                        予定再作成
                      </a>
                      --}}
                      @endif
                    </div>
                  </div>
                </li>
                @endif
            </ul>
          @endforeach
        @else
        <div class="alert">
          <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    @component('components.search_word', ['search_word' => $search_word])
    @endcomponent
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        フィルタリング
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="/{{$domain}}?status=new" class="nav-link @if($_status=="new") active @endif">
          <i class="fa fa-exclamation-triangle nav-icon"></i>未対応
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=confirm" class="nav-link @if($_status=="confirm") active @endif">
          <i class="fa fa-check-circle nav-icon"></i>予定確認中
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=fix" class="nav-link @if($_status=="fix") active @endif">
          <i class="fa fa-calendar-alt nav-icon"></i>授業予定
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=rest,cancel" class="nav-link @if($_status=="rest,cancel") active @endif">
          <i class="fa fa-ban nav-icon"></i>キャンセル
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=absence,presence" class="nav-link @if($_status=="absence,presence") active @endif">
          <i class="fa fa-history nav-icon"></i>履歴
        </a>
      </li>
      {{--
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link">
          <i class="fa fa-list-alt nav-icon"></i>すべて
        </a>
      </li>
      --}}
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
  <dt>
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="">
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
  </dt>
@endsection
