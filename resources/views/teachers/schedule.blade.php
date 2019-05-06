@section('title')
  {{$domain_name}}授業スケジュール
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


@section('contents')
<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-calendar mr-1"></i>
            {{$list_title}}
          </h3>
          <div class="card-tools">
            <ul class="pagination pagination-sm m-0 float-right">
              @if($_maxpage>1)
              <li class="page-item"><a class="page-link" href="{{sprintf('/%s/%d/schedule?list=%s&_page=%d&_line=%d', $domain, $item->id, $list, 0, $_line)}}">«</a></li>
              @for($i=$_page-2;$i<$_page+3;$i++)
                @if($i<0)
                  @continue
                @endif
                @if($i>$_maxpage) @continue @endif
                <li class="page-item">
                  @if($i==$_page)
                  <span class="page-link text-dark bg-primary">{{$i+1}}</span>
                  @else
                  <a class="page-link" href="{{sprintf('/%s/%d/schedule?list=%s&_page=%d&_line=%d', $domain, $item->id, $list, $i, $_line)}}">{{$i+1}}</a>
                  @endif
                </li>
              @endfor
              <li class="page-item"><a class="page-link" href="{{sprintf('/%s/%d/schedule?list=%s&_page=%d&_line=%d', $domain, $item->id, $list, $_maxpage, $_line)}}">»</a></li>
              @endif
            </ul>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if(count($calendars) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($calendars as $calendar)
            <li class="col-12" accesskey="" target="">
              <div class="row">
                <div class="col-5 col-lg-4 col-md-4">
                  <i class="fa fa-calendar mx-1"></i>{{$calendar["date"]}}
                  <i class="fa fa-clock mx-1"></i>{{$calendar["timezone"]}}
                  <br>
                  <i class="fa fa-map-marker mx-1"></i>{{$calendar->place()}}
                </div>
                <div class="col-7 col-lg-4 col-md-4">
                  @foreach($calendar->members as $member)
                    @if($member->user->details()->role==="student")
                      <a href="/students/{{$member->user->details()->id}}">
                        <i class="fa fa-user-graduate mr-2"></i>
                        {{$member->user->details()->name}}
                      </a>
                    @endif
                  @endforeach
                  <br>
                  @foreach($calendar['subject'] as $subject)
                  <span class="text-xs mx-2">
                    <small class="badge badge-primary mt-1 mr-1">
                      {{$subject}}
                    </small>
                  </span>
                  @endforeach
                </div>
                <div class="col-12 col-lg-4 col-md-4 text-sm mt-1">
                  <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="詳細" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" role="button" class="btn btn-outline-{{config('status_style')[$calendar->status]}} btn-sm float-left mr-1 w-100">
                    <i class="fa fa-file-alt mr-1"></i>{{$calendar["status_name"]}}
                  </a>
                  <br>
                  @if($user->role==="teacher" || $user->role==="manager" )
                    @if($calendar["status"]==="fix" && date('Ymd', strtotime($calendar["start_time"])) === date('Ymd'))
                    <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="出欠を取る" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/presence?origin={{$domain}}&item_id={{$item->id}}&page=schedule" role="button" class="btn btn-success btn-sm w-100 mt-1">
                      <i class="fa fa-user-check mr-1"></i>
                      出欠確認
                    </a>
                    @elseif($calendar["status"]==="new")
                    <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="予定を確定する" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/confirm?origin={{$domain}}&item_id={{$item->id}}&page=schedule" role="button" class="btn btn-warning btn-sm w-100 mt-1">
                      <i class="fa fa-user-check mr-1"></i>
                      予定を確定する
                    </a>
                    @elseif($calendar["status"]==="confirm")
                    <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="予定連絡" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/remind?origin={{$domain}}&item_id={{$item->id}}&page=schedule" role="button" class="btn btn-warning btn-sm w-100 mt-1">
                      <i class="fa fa-user-check mr-1"></i>
                      予定連絡
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
      </div>
    </div>
  </div>
</section>
@endsection
