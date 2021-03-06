@if($user->role==="teacher" || $user->role==="manager" || $user->role=='staff')
  @if($calendar->status=="fix")
    @if($calendar->is_passed()==false)
      {{-- 未来の予定に対し、休講 --}}
      @if($calendar->work==9)
        {{-- 事務：勤務予定→休み連絡 --}}
        <a title="{{$calendar->id}}" href="javascript:void(0);" page_title="休み連絡" page_form="dialog" page_url="/calendars/{{$calendar->id}}/status_update/rest?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-secondary btn-sm ml-1">
          <i class="fa fa-minus-circle mr-1"></i>休み連絡する
        </a>
      @elseif($calendar->schedule_type_code()!="training")
        <a title="{{$calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.ask_lecture_cancel')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}/status_update/lecture_cancel?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-secondary btn-sm ml-1">
          <i class="fa fa-ban mr-1"></i>
          @if($calendar->work==9)
          {{__('labels.calendar_button_holiday')}}
          @else
          {{__('labels.ask_lecture_cancel')}}
          @endif
        </a>
      @endif
    @elseif($calendar->schedule_type_code()!="training")
      {{-- 過ぎた予定に対し、出欠 --}}
      <a title="{{$calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.calendar_button_attendance')}}{{__('labels.check')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}/status_update/presence?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-success btn-sm ml-1">
        <i class="fa fa-user-check mr-1"></i>
        @if($calendar->work==9)
        {{__('labels.calendar_button_working')}}
        @else
        {{__('labels.calendar_button_attendance')}}
        @endif
      </a>
    @endif
  @elseif($calendar->status==="new")
  {{-- 講師予定確認済み --}}
  <a title="{{$calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.schedule_fix')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}/status_update/confirm?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm ml-1">
    <i class="fa fa-user-check mr-1"></i>
    {{__('labels.schedule_fix')}}
  </a>
  @elseif($calendar->status==="confirm")
  {{-- 生徒へ再度通知連絡 --}}
  <a title="{{$calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.schedule_remind')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}/status_update/remind?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm ml-1">
    <i class="fa fa-envelope mr-1"></i>
    <span class="ml-1 btn-label">
    {{__('labels.schedule_remind')}}
  </a>
  @elseif($calendar->status==="rest" && $calendar->work==9)
  {{-- 事務作業で休みの場合、休み取り消し --}}
  <a title="{{$calendar->id}}" href="javascript:void(0);" page_title="休み取り消し" page_form="dialog" page_url="/calendars/{{$calendar->id}}/status_update/rest_cancel?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm ml-1">
    <i class="fa fa-minus-circle"></i>
    <span class="ml-1">
      休み取消
    </span>
  </a>
  @elseif($calendar->status=="dummy" && $user->role==="manager")
  {{-- ダミー解除 --}}
  <a title="{{$calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.dummy_release')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}" role="button" class="btn btn-primary btn-sm ml-1">
    <i class="fa fa-unlock-alt mr-1"></i>
    <span class="ml-1 btn-label">
    {{__('labels.dummy_release')}}
  </a>
  @else
  {{-- 参照のみ --}}
  <a href="javascript:void(0);" title="{{$calendar->id}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}" role="button" class="btn btn-outline-{{config('status_style')[$calendar->status]}} btn-sm ml-1">
      @if($calendar->is_checked()==true)
        <i class="fa fa-lock"></i>
        <span class="ml-1 btn-label">
          {{__('labels.monthly_confirmed')}}
        </span>
      @else
      <i class="fa fa-file-alt"></i>
      <span class="ml-1 btn-label">
        {{__('labels.details')}}
      </span>
      @endif
  </a>
  @endif
@endif
@if(!($calendar->is_season_lesson()==true || $calendar->work == 9) )
  {{--代講依頼　土日、期間講習は除く--}}
  @if( $calendar->is_passed()==false )
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.ask_teacher_change')}}" page_form="dialog" page_url="/calendars/{{$calendar['id']}}/asks/teacher_change" role="button" class="btn btn-danger btn-sm ml-1">
    <i class="fa fa-sync mr-1"></i>{{__('labels.ask_teacher_change')}}
  </a>
  @elseif($user->role==="manager")
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="{{__('labels.teacher_change')}}" page_form="dialog" page_url="/calendars/{{$calendar['id']}}/asks/teacher_change?maintenance=true" role="button" class="btn btn-info btn-sm ml-1">
    <i class="fa fa-sync mr-1"></i>{{__('labels.teacher_change')}}
  </a>
  @endif
@endif

@if($calendar->exist_rest_student()==true && $user->role==="manager")
{{-- TODO 休み種別変更 --}}
<a title="{{$calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.rest_change')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}/rest_change?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-danger btn-sm ml-1">
  <i class="fa fa-exclamation-circle" title="{{$calendar->status}}"></i>
  <span class="ml-1 btn-label">
    {{__('labels.rest_change')}}
  </span>
</a>
@endif

@if(($calendar->status==="presence" || $calendar->status==="absence") && ($user->role==="manager" || ($user->role==="teacher" && ($calendar->trial_id>0 && $calendar->is_checked()==false))))
{{-- 出欠変更 --}}
<a title="{{$calendar->id}}" href="javascript:void(0);" page_title="{{__('labels.calendar_button_attendance')}}{{__('labels.edit')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}/status_update/presence?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm ml-1">
  <i class="fa fa-wrench" title="{{$calendar->status}}"></i>
  <span class="ml-1 btn-label">
    出欠変更
  </span>
</a>
@endif
@if(!($user->role==="teacher" && $calendar->is_checked()==true))
<a href="javascript:void(0);" page_title="{{__('labels.schedule_edit')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}/edit" role="button" class="btn btn-default btn-sm ml-1">
  <i class="fa fa-edit"></i>
</a>
@endif
@if($user->role==="manager")
<a href="javascript:void(0);" page_title="{{__('labels.schedule_delete')}}" page_form="dialog" page_url="/calendars/{{$calendar->id}}?action=delete" role="button" class="btn btn-default btn-sm ml-1">
  <i class="fa fa-trash"></i>
</a>
@endif
@if($user->role==="manager")
  <a href="javascript:void(0);" page_title="メンバー設定" page_form="dialog" page_url="/calendars/{{$calendar->id}}/members/setting" role="button" class="btn btn-default btn-sm ml-1">
    <i class="fa fa-user-cog"></i>
  </a>
  @if($calendar->is_group()==true)
  <a href="javascript:void(0);" page_title="メンバー追加" page_form="dialog" page_url="/calendars/{{$calendar->id}}/members/create" role="button" class="btn btn-default btn-sm ml-1">
    <i class="fa fa-user-plus"></i>
  </a>
  @endif
@endif
