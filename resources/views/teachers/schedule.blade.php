@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


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
                @foreach($item["tags"] as $tag)
                <small class="badge badge-secondary mt-1 mr-1">
                  {{$tag->name()}}
                </small>
                @endforeach
              </h6>
            @endslot
        @endcomponent
			</div>
			<div class="col-md-8">
				@yield('comments')
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
            <i class="fa fa-calendar mr-1"></i>
            授業予定
          </h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if(count($calendars) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($calendars as $calendar)
              @if($calendar["status"]==="new" || $calendar["status"]==="confirm" || $calendar["status"]==="cancel")
                @continue;
              @endif
            <li class="col-12" accesskey="" target="">
              <div class="row">
                <div class="col-5 col-lg-4 col-md-4">
                  <i class="fa fa-calendar mr-1"></i>{{$calendar["date"]}}
                  <br>
                  <i class="fa fa-clock mr-1"></i>{{$calendar["timezone"]}}
                </div>
                <div class="col-7 col-lg-4 col-md-4">
                  <i class="fa fa-user-graduate mr-2"></i>
                  {{$calendar["student_name"]}}</td>
                  <br>
                  <small class="badge badge-primary mt-1 mr-1">
                    {{$calendar["subject"]}}
                  </small>
                </div>
                <div class="col-12 col-lg-4 col-md-4 text-sm mt-1">
                  <a href="javascript:void(0);" page_title="詳細" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" role="button" class="btn btn-outline-info btn-sm float-left mr-1 w-100">
                    <i class="fa fa-file-alt mr-1"></i>{{$calendar["status_name"]}}
                  </a>
                  <br>
                  @if($calendar["status"]==="fix" && date('Ymd', strtotime($calendar["start_time"])) === date('Ymd'))
                    <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="出欠を取る" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/presence?_page_origin={{$domain}}_{{$item->id}}_calendar&mode=list" role="button" class="btn btn-success btn-sm w-100 mt-1">
                      <i class="fa fa-user-check mr-1"></i>
                      出欠確認
                    </a>
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
      <!-- /.card -->
    </div>
  </div>
</section>
@endsection
