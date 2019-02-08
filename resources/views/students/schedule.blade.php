@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')
@include('dashboard.widget.milestones')

@section('contents')
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
            <li class="col-12" accesskey="" target="">
              <div class="row">
                <div class="col-5 col-lg-4 col-md-4">
                  <i class="fa fa-calendar mr-1"></i>{{$calendar["date"]}}
                  <br>
                  <i class="fa fa-clock mr-1"></i>{{$calendar["timezone"]}}
                </div>
                <div class="col-7 col-lg-4 col-md-4">
                  <i class="fa fa-user-tie mr-2"></i>
                  {{$calendar["teacher_name"]}}</td>
                  <br>
                  <small class="badge badge-primary mt-1 mr-1">
                    {{$calendar["subject"]}}
                  </small>
                </div>
                <div class="col-12 col-lg-4 col-md-4 text-sm mt-1">
                  <a href="javascript:void(0);" page_title="詳細" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" role="button" class="btn btn-{{$calendar->status_style()}} btn-sm float-left mr-1 w-100">
                    <i class="fa fa-file-alt mr-1"></i>{{$calendar->status_name()}}
                  </a>
                  <br>
                  @if($user->role!=="manager" && $user->role!=="teacher" && $calendar["status"]==="fix")
                  <a href="javascript:void(0);" page_title="休み連絡" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/rest?origin={{$domain}}&item_id={{$item->id}}&page=calendar" role="button" class="btn btn-danger btn-sm float-left mt-1 mr-1 w-100" @if($calendar["status"]!=="fix") disabled @endif>
                    <i class="fa fa-minus-circle mr-1"></i>休み連絡する
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
