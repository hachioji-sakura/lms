@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends($domain.'.page')
@include($domain.'.menu')

@section('sub_contents')
<div class="row mb-2">
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a href="/{{$domain}}/{{$item->id}}/month_work" class="">
    <div class="info-box">
      <span class="info-box-icon bg-success">
        <i class="fa fa-tasks"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">{{__('labels.work_record')}}</b>
        <span class="text-sm">{{__('labels.work_record_description')}}</span>
      </div>
    </div>
    </a>
  </div>
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a href="/{{$domain}}/{{$item->id}}/calendar" class="">
    <div class="info-box">
      <span class="info-box-icon bg-success">
        <i class="fa fa-calendar"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">{{__('labels.calendar_page')}}</b>
        <span class="text-sm">{{__('labels.calendar_page_description')}}</span>
      </div>
    </div>
    </a>
  </div>
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a href="/{{$domain}}/{{$item->id}}/messages" class="">
    <div class="info-box">
      <span class="info-box-icon bg-warning">
        <i class="fa fa-envelope"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">{{__('labels.messages')}}</b>
        <span class="text-sm">{{__('labels.messages_description')}}</span>
      </div>
    </div>
    </a>
  </div>
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a class="" href="/{{$domain}}/{{$item->id}}/ask?list=teacher_change">
    <div class="info-box">
      <span class="info-box-icon bg-warning">
        <i class="fa fa-sync"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">
          {{__('labels.ask_teacher_change')}}
          @if($teacher_change_count > 0)
          <span class="badge badge-danger float-right">{{$teacher_change_count}}</span>
          @endif
        </b>
        <span class="text-sm">{{__('labels.ask_teacher_change_description')}}</span>
      </div>

    </div>
    </a>
  </div>
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a href="/{{$domain}}/{{$item->id}}/schedule?list=today" class="">
    <div class="info-box">
      <span class="info-box-icon bg-info">
        <i class="fa fa-clock"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">
          {{__('labels.today_schedule_list')}}
          @if($today_count > 0)
          <span class="badge badge-primary float-right">{{$today_count}}</span>
          @endif
        </b>
        <span class="text-sm">{{__('labels.today_schedule_list_description')}}</span>
        <b class="info-box-text text-lg"></b>
        <span class="text-sm"></span>
      </div>
    </div>
    </a>
  </div>
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a class="" href="/{{$domain}}/{{$item->id}}/schedule?list=confirm">
    <div class="info-box">
      <span class="info-box-icon bg-info">
        <i class="fa fa-hourglass"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">
          {{__('labels.adjust_schedule_list')}}
          @if($confirm_count > 0)
          <span class="badge badge-danger float-right">{{$confirm_count}}</span>
          @endif
        </b>
        <span class="text-sm">{{__('labels.adjust_schedule_list_description')}}</span>
      </div>

    </div>
    </a>
  </div>
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a class="" href="/{{$domain}}/{{$item->id}}/schedule?list=exchange">
    <div class="info-box">
      <span class="info-box-icon bg-info">
        <i class="fa fa-exchange-alt"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">
          {{__('labels.exchange_schedule_list')}}
          @if($exchange_count > 0)
          <span class="badge badge-danger float-right">{{$exchange_count}}</span>
          @endif
        </b>
        <span class="text-sm">{{__('labels.exchange_schedule_list_description')}}</span>
      </div>

    </div>
    </a>
  </div>
  {{--
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a class="" href="javascript:void(0);"  page_form="dialog" page_url="/calendars/create?teacher_id={{$item->id}}" page_title="{{__('labels.schedule_add')}}">
    <div class="info-box">
      <span class="info-box-icon bg-primary">
        <i class="fa fa-plus"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">{{__('labels.schedule_add')}}</b>
        <span class="text-sm">{{__('labels.schedule_add_description')}}</span>
      </div>
    </div>
    </a>
  </div>
  --}}
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a class="" href="/{{$domain}}/{{$item->id}}/calendar_settings?list=fix_list" >
    <div class="info-box">
      <span class="info-box-icon bg-primary">
        <i class="fa fa-user-clock"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">
          {{__('labels.regular_schedule_list')}}
          @if($fix_list_setting_count > 0)
          <span class="badge badge-primary float-right">{{$fix_list_setting_count}}</span>
          @endif
        </b>
        <span class="text-sm">{{__('labels.regular_schedule_list_description')}}</span>
      </div>
    </div>
    </a>
  </div>
  <div class="col-12 col-lg-4 col-md-6 mb-1">
    <a class="" href="/{{$domain}}/{{$item->id}}/calendar_settings?list=confirm_list" >
    <div class="info-box">
      <span class="info-box-icon bg-primary">
        <i class="fa fa-exclamation-triangle"></i>
      </span>
      <div class="info-box-content text-dark">
        <b class="info-box-text text-lg">
          {{__('labels.regular_schedule_confirm')}}
          @if($confirm_list_setting_count > 0)
          <span class="badge badge-warning float-right">{{$confirm_list_setting_count}}</span>
          @endif
        </b>
        <span class="text-sm">{{__('labels.regular_schedule_confirm_description')}}</span>
      </div>
    </div>
    </a>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title" id="charge_students">
          <i class="fa fa-users mr-1"></i>
          {{__('labels.charge_student')}}
        </h3>
        <div class="card-tools">
          @component('components.search_word', ['search_word' => $search_word])
          @endcomponent
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        @if(count($charge_students) > 0)
        <ul class="mailbox-attachments clearfix row">
          @foreach($charge_students as $student)
          <li class="col-lg-3 col-md-4 col-12" accesskey="" target="">
            <div class="row">
              <div class="col-6 text-center">
                <a alt="student_name" href="/students/{{$student->id}}">
                  <img src="{{$student->user->details()->icon}}" class="img-circle mw-64px w-50">
                  <br>
                  <ruby style="ruby-overhang: none">
                    <rb>{{$student->name()}}</rb>
                    <rt>{{$student->kana()}}</rt>
                  </ruby>
                </a>
              </div>
              <div class="col-6 text-sm">
                @if(!empty($student->current_calendar))
                    <i class="fa fa-calendar mr-1"></i>{{$student->current_calendar["dateweek"]}}
                    <br>
                    <i class="fa fa-clock mr-1"></i>{{$student->current_calendar->timezone}}
                    <br>
                    <i class="fa fa-map-marker mr-1"></i>{{$student->current_calendar["place_floor_name"]}}
                    <br>
                    <small title="{{$item["id"]}}" class="badge badge-{{config('status_style')[$student->current_calendar['status']]}} mt-1 mr-1">{{$student->current_calendar["status_name"]}}</small>
                    {{--
                    <br>
                    @foreach($student->current_calendar['subject'] as $subject)
                    <span class="text-xs mx-2">
                      <small class="badge badge-primary mt-1 mr-1">
                        {{$subject}}
                      </small>
                    </span>
                    @endforeach
                    --}}
                @else
                -
                @endif
              </div>
              <div class="col-12 text-sm">
                @if(isset($student->current_calendar))
                  @if($user->role==="teacher" && $student->current_calendar->status==="fix" && date('Ymd', strtotime($student->current_calendar->start_time)) === date('Ymd'))
                    <a title="{{$student->current_calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.schedule_presence')}}" page_form="dialog" page_url="/calendars/{{$student->current_calendar->id}}/status_update/presence?origin={{$domain}}&item_id={{$item->id}}&student_id={{$student->id}}" role="button" class="btn btn-info btn-sm w-100 mt-1">
                      <i class="fa fa-user-check mr-1"></i>
                      {{__('labels.details')}}
                    </a>
                  @elseif($student->current_calendar->status==="confirm")
                  {{-- @elseif($student->current_calendar->status==="fix" || $student->current_calendar->status==="confirm") --}}
                    {{-- 予定確認 --}}
                    <a title="{{$student->current_calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.schedule_remind')}}" page_form="dialog" page_url="/calendars/{{$student->current_calendar->id}}/status_update/remind" role="button" class="btn btn-warning btn-sm w-100 mt-1">
                      <i class="fa fa-envelope mr-1"></i>
                      {{__('labels.schedule_remind')}}
                    </a>
                  @elseif($student->current_calendar->status==="presence")
                    {{-- 出席済み --}}
                    <a title="{{$student->current_calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.schedule_details')}}" page_form="dialog" page_url="/calendars/{{$student->current_calendar->id}}" role="button" class="btn btn-success btn-sm w-100 mt-1">
                      <i class="fa fa-check-circle mr-1"></i>
                      {{__('labels.details')}}
                    </a>
                  @elseif($student->current_calendar->status==="new")
                    {{-- 予定下書き --}}
                    <a title="{{$student->current_calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.schedule_fix')}}" page_form="dialog" page_url="/calendars/{{$student->current_calendar->id}}/status_update/confirm" role="button" class="btn btn-secondary btn-sm w-100 mt-1">
                      <i class="fa fa-calendar-check mr-1"></i>
                      {{__('labels.details')}}
                    </a>
                  @else
                    <a title="{{$student->current_calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.schedule_details')}}" page_form="dialog" page_url="/calendars/{{$student->current_calendar->id}}" role="button" class="btn btn-secondary btn-sm w-100 mt-1">
                      <i class="fa fa-file-alt mr-1"></i>
                      {{__('labels.details')}}
                    </a>
                  @endif
                @endif
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
