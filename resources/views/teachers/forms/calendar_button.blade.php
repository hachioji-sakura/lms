@if($user->role==="teacher" || $user->role==="manager" )
{{--
  @if($calendar["status"]==="fix")
--}}
@if($calendar["status"]=="fix" && $calendar["is_passed"]==true)
  {{-- 授業当日出欠 --}}
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="出欠を取る" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/presence?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-success btn-sm">
    <i class="fa fa-user-check mr-1"></i>
    @if($calendar->is_management()==true)
    出勤確認
    @else
    出欠確認
    @endif
  </a>
  {{--TODO 休講は出欠と排反にする
@elseif($calendar["status"]=="fix" && $calendar["is_passed"]==false)
    --}}
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="休講依頼" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/lecture_cancel?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-secondary btn-sm">
    <i class="fa fa-ban mr-1"></i>
    @if($calendar->is_management()==true)
    休暇
    @else
    休講依頼
    @endif
  </a>
  @elseif($calendar["status"]==="new")
  {{-- 講師予定確認済み --}}
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="予定を確定する" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/confirm?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm">
    <i class="fa fa-user-check mr-1"></i>
    予定を確定する
  </a>
  @elseif($calendar["status"]==="confirm")
  {{-- 生徒へ再度通知連絡 --}}
  <a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="予定連絡" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/remind?origin={{$domain}}&item_id={{$teacher->id}}&page=schedule" role="button" class="btn btn-warning btn-sm">
    <i class="fa fa-user-check mr-1"></i>
    予定連絡
  </a>
  @else
  {{-- 参照のみ --}}
  <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="詳細" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" role="button" class="btn btn-outline-{{config('status_style')[$calendar->status]}} btn-sm mr-1">
    <i class="fa fa-file-alt mr-1"></i>{{$calendar["status_name"]}}
  </a>
  @endif
@endif
@if($calendar->is_exchange_target()==true)
<a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="振替登録" page_form="dialog" page_url="/calendars/create?exchanged_calendar_id={{$calendar["id"]}}" role="button" class="btn btn-default btn-sm mr-1">
  <i class="fa fa-exchange-alt mr-1"></i>
  振替登録
</a>
@endif
@if($calendar["status"]=="new")
<a title="{{$calendar["id"]}}" href="javascript:void(0);" page_title="予定変更" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/edit" role="button" class="btn btn-default btn-sm mx-1">
  <i class="fa fa-edit mr-1"></i>
  変更
</a>
@endif
