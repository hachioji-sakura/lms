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
                    @if($member->user->details('students')->role==="student")
                      <a href="/students/{{$member->user->details('students')->id}}" class="mr-2" target=_blank>
                        <i class="fa fa-user-graduate"></i>
                        {{$member->user->details('students')->name}}
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
                  @component('teachers.forms.calendar_button', ['teacher'=>$item, 'calendar' => $calendar, 'user'=>$user, 'domain'=>$domain, 'domain_name'=>$domain_name])
                  @endcomponent
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
