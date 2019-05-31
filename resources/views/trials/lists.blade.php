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
          <ul class="mailbox-attachments clearfix row">
            @foreach($items as $item)
            <li class="col-12" accesskey="" target="">
              <a href="trials/{{$item->id}}">
                <div class="row">
                  <div class="col-12 col-lg-6 col-md-6 mt-1">
                    <span class="time"><i class="fa fa-clock mx-1"></i>{{$item['create_date']}}申込</span>
                    <span class="text-xs">
                      <small class="badge badge-{{config('status_style')[$item['status']]}} p-1 mr-1">
                        <i class="fa fa-file-alt mr-1"></i>{{$item['status_name']}}
                      </small>
                    </span>
                    @foreach($item["tagdata"]["lesson"] as $label)
                    <span class="text-xs">
                      <small class="badge badge-primary p-1 mr-1">
                        <i class="fa fa-chalkboard mr-1"></i>
                        {{$label}}
                      </small>
                    </span>
                    @endforeach
                    @foreach($item["tagdata"]["lesson_place"] as $label)
                    <span class="text-xs">
                      <small class="badge badge-success p-1 mr-1">
                        <i class="fa fa-map-marker mr-1"></i>
                        {{$label}}
                      </small>
                    </span>
                    @endforeach
                    @foreach($item->trial_students as $trial_student)
                    <span class="text-xs">
                      <small class="badge badge-info p-1 mr-1">
                        <i class="fa fa-user mr-1"></i>
                        {{$trial_student->student->name()}}
                        （{{$trial_student->student->grade()}}）
                      </small>
                    </span>
                    @endforeach
                  </div>
                  <div class="col-12 col-lg-6 col-md-6 mt-1">
                      第1希望:{{$item['date1']}}</span><br>
                      第2希望:{{$item['date2']}}</span><br>
                      第3希望:{{$item['date3']}}</span>
                  </div>
                </div>
              </a>
            </li>
            @endforeach
          </ul>
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
