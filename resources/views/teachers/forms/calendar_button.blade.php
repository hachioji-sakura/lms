@if($user->role==="teacher" || $user->role==="manager" || $user->role=='staff')
{{--
  @if($calendar["status"]==="fix")
--}}
@if($calendar["status"]=="fix")
  {{-- 授業当日出欠 --}}
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.calendar_button_attendance')}}{{__('labels.check')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/presence?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-success btn-sm">
    <i class="fa fa-user-check mr-1"></i>
    @if($calendar->is_management()==true)
    {{__('labels.calendar_button_working')}}
    @else
    {{__('labels.calendar_button_attendance')}}
    @endif
  </a>
    @if($calendar["is_passed"]==false)
      <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.ask_lecture_cancel')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/lecture_cancel?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-secondary btn-sm">
        <i class="fa fa-ban mr-1"></i>
        @if($calendar->is_management()==true)
        {{__('labels.calendar_button_holiday')}}
        @else
        {{__('labels.ask_lecture_cancel')}}
        @endif
      </a>
    @endif
  @elseif($calendar["status"]==="new")
  {{-- 講師予定確認済み --}}
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.schedule_fix')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/confirm?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm">
    <i class="fa fa-user-check mr-1"></i>
    {{__('labels.schedule_fix')}}
  </a>
  @elseif($calendar["status"]==="confirm")
  {{-- 生徒へ再度通知連絡 --}}
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.schedule_remind')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/remind?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm">
    <i class="fa fa-user-check mr-1"></i>
    {{__('labels.schedule_remind')}}
  </a>
  @else
  {{-- 参照のみ --}}
  <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" role="button" class="btn btn-outline-{{config('status_style')[$calendar->status]}} btn-sm mr-1">
    <i class="fa fa-file-alt mr-1"></i>{{__('labels.details')}}
  </a>
  @endif
@endif
@if($calendar->is_exchange_target()==true)
<a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.exchange_add')}}" page_form="dialog" page_url="/calendars/create?exchanged_calendar_id={{$calendar["id"]}}" role="button" class="btn btn-default btn-sm mr-1">
  <i class="fa fa-exchange-alt mr-1"></i>
  {{__('labels.exchange_add')}}
</a>
@endif
@if($calendar["status"]=="new")
<a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.schedule_edit')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/edit" role="button" class="btn btn-default btn-sm mx-1">
  <i class="fa fa-edit mr-1"></i>
  {{__('labels.schedule_edit')}}
</a>
@endif
