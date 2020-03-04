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
          体験申し込み一覧
        </h3>
      </div>
      <div id="trial_list" class="card-body table-responsive p-3">
        @if(count($items) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($items as $item)
            <li class="col-12" accesskey="" target="">
                <div class="row">
                  <div class="col-12 col-lg-4 col-md-6 mt-1">
                    <a href="trials/{{$item->id}}">
                    @foreach($item->trial_students as $trial_student)
                    @if(!isset($trial_student->student))
                      @continue
                    @endif
                    <span class="text-xs ml-1">
                        <i class="fa fa-user mr-1"></i>
                        {{$trial_student->student->name()}} 様
                        （{{$trial_student->student->grade()}}）<br>
                      </small>
                    @endforeach
                    </a>
                    <span class="text-sm time">申込日:{{$item['create_date']}}</span>
                    <span class="text-xs ml-2">
                      <small class="badge badge-{{config('status_style')[$item['status']]}} p-1 mr-1">
                        {{$item['status_name']}}
                      </small>
                    </span>
                    <br>
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
                  </div>
                  <div class="col-12 col-lg-4 col-md-6 mt-1 text-sm">
                      第1希望:{{$item['date1']}}</span><br>
                      第2希望:{{$item['date2']}}</span><br>
                      第3希望:{{$item['date3']}}</span>
{{--
<br>
                      第4希望:{{$item['date4']}}</span><br>
                      第5希望:{{$item['date5']}}</span>
--}}
                  </div>
                  <div class="col-12 col-lg-4 mt-1 text-sm">
                    <a href="trials/{{$item->id}}" role="" class="mr-1" style="text-decoration: underline">
                      <i class="fa fa-file"></i>
                      {{__('labels.details')}}
                    </a>
                    <a title="{{$item["id"]}}" href="javascript:void(0);" page_title="体験申し込み編集" page_form="dialog" page_url="/trials/{{$item["id"]}}/edit" role="" class="mr-1"  style="text-decoration: underline">
                      <i class="fa fa-edit"></i>
                      {{__('labels.edit')}}
                    </a>
                    <br>
                    <a href="trials/{{$item->id}}/to_calendar" role="button" class="btn btn-info btn-sm mt-1">
                      <i class="fa fa-plus mr-1"></i>
                      体験授業登録
                    </a>
                    <a href="trials/{{$item->id}}/to_calendar_setting" role="button" class="btn btn-primary btn-sm mt-1">
                      <i class="fa fa-plus mr-1"></i>
                      通常授業登録
                    </a>
                  </div>
                </div>
            </li>
            @endforeach
          </ul>
        @else
        <div class="alert">
          <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
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
        申し込み状態
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="/{{$domain}}?status=new" class="nav-link @if($_status=="today") active @endif">
          <i class="fa fa-exclamation-triangle nav-icon"></i>
          <p>
            未対応
            @if($new_count > 0)
            <span class="badge badge-danger right">{{$new_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=confirm" class="nav-link @if($_status=="confirm") active @endif">
          <i class="fa fa-calendar-alt nav-icon"></i>
          <p>
            体験授業調整中
            @if($confirm_count > 0)
            <span class="badge badge-warning right">{{$confirm_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=fix" class="nav-link @if($_status=="fix") active @endif">
          <i class="fa fa-calendar-plus nav-icon"></i>
          <p>
            体験授業確定
            @if($fix_count > 0)
            <span class="badge badge-primary right">{{$fix_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=presence" class="nav-link @if($_status=="presence") active @endif">
          <i class="fa fa-calendar-check nav-icon"></i>
          <p>
            体験授業完了
            @if($presence_count > 0)
            <span class="badge badge-success right">{{$presence_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=complete" class="nav-link @if($_status=="complete") active @endif">
          <i class="fa fa-check-circle nav-icon"></i>
          <p>
            入会案内連絡済
            @if($complete_count > 0)
            <span class="badge badge-secondary right">{{$complete_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?status=rest,cancel" class="nav-link @if($_status=="rest,cancel") active @endif">
          <i class="fa fa-ban nav-icon"></i>
          <p>
            キャンセル
            @if($cancel_count > 0)
            <span class="badge badge-secondary right">{{$cancel_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link @if(empty($_status)) active @endif">
          <i class="fa fa-history nav-icon"></i>
          <p>
            履歴
          </p>
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
{{--
  <dt>
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="">
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
  </dt>
--}}
@endsection
