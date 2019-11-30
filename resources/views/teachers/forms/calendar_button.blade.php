@if($user->role==="teacher" || $user->role==="manager" || $user->role=='staff')
  @if($calendar["status"]=="fix")
    @if($calendar["is_passed"]==false)
      {{-- 未来の予定に対し、休講 --}}
      @if($calendar->work==9)
        {{-- 事務：勤務予定→休み連絡 --}}
        <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="休み連絡" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/rest?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-secondary btn-sm">
          <i class="fa fa-minus-circle mr-1"></i>休み連絡する
        </a>
      @else
        <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.ask_lecture_cancel')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/lecture_cancel?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-secondary btn-sm">
          <i class="fa fa-ban mr-1"></i>
          @if($calendar->work==9)
          {{__('labels.calendar_button_holiday')}}
          @else
          {{__('labels.ask_lecture_cancel')}}
          @endif
        </a>
      @endif
    @else
      {{-- 過ぎた予定に対し、出欠 --}}
      <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.calendar_button_attendance')}}{{__('labels.check')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/presence?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-success btn-sm">
        <i class="fa fa-user-check mr-1"></i>
        @if($calendar->work==9)
        {{__('labels.calendar_button_working')}}
        @else
        {{__('labels.calendar_button_attendance')}}
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
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.schedule_remind')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/remind?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm">
    <i class="fa fa-user-check mr-1"></i>
    {{__('labels.schedule_remind')}}
  </a>
  @elseif($calendar["status"]==="rest" && $calendar->work==9)
  {{-- 事務作業で休みの場合、休み取り消し --}}
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="休み取り消し" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/rest_cancel?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm">
    <i class="fa fa-minus-circle mr-1"></i>
    休み取消
  </a>
  @elseif($calendar["is_passed"]==true && $calendar->exist_rest_student()==true && $user->role==="manager")
  {{-- TODO 休み種別変更 --}}
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.rest_change')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/rest_change?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm">
    <i class="fa fa-exclamation-circle mr-1" title="{{$calendar["status"]}}"></i>
    {{__('labels.rest_change')}}
  </a>
  @elseif($calendar["status"]==="presence")
  {{-- 過ぎた予定に対し、出欠 --}}
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.calendar_button_attendance')}}{{__('labels.check')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/presence?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm">
    <i class="fa fa-exclamation-circle mr-1" title="{{$calendar["status"]}}"></i>
    出欠変更
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
<a href="javascript:void(0);" page_title="{{__('labels.schedule_edit')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/edit" role="button" class="btn btn-default btn-sm mx-1">
  <i class="fa fa-edit"></i>
</a>
@if($user->role==="manager")
<a href="javascript:void(0);" page_title="{{__('labels.schedule_delete')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}?action=delete" role="button" class="btn btn-default btn-sm mx-1">
  <i class="fa fa-times"></i>
</a>
@endif
