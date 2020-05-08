@if(count($calendars) > 0)
<ul class="mailbox-attachments clearfix row">
  @foreach($calendars as $calendar)
  <li class="col-12 p-0" accesskey="" target="">
    <div class="row p-2
    @if($calendar->is_cancel_status()==true)
    calendar_rest
    @endif
    ">
      <div class="col-5 col-lg-4 col-md-4">
        <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}?student_id={{$item->id}}" >
        <i class="fa fa-calendar mx-1"></i>{{$calendar["dateweek"]}}
        <small title="{{$calendar["id"]}}" class="badge badge-{{config('status_style')[$calendar['status']]}} mt-1 mx-1">{{$calendar["status_name"]}}</small>
        <br>
        <i class="fa fa-clock mx-1"></i>{{$calendar["timezone"]}} / {{$calendar->teaching_type_name()}}
        <br>
        @if($calendar->is_online()==true)
        <small class="badge badge-info mx-1">
          <i class="fa fa-globe"></i>
          {{__('labels.online')}}
        </small>
        @else
        <i class="fa fa-map-marker mx-1"></i>{{$calendar["place_floor_name"]}}
        @endif
        </a>
      </div>
      <div class="col-7 col-lg-4 col-md-4">
        <i class="fa fa-user-tie mr-2"></i>
        {{$calendar['teacher_name']}}
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
        <a href="javascript:void(0);" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}?student_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1 w-100">
          <i class="fa fa-file-alt mr-1"></i>詳細
        </a>
        <br>
        {{--
          TODO　将来的に事務のみ代理連絡可能にする
        @if($user->role!=="teacher" && $calendar->get_member($item->user_id)->status==="fix")
        --}}
        @if($user->role!=="teacher" && $calendar->get_member($item->user_id)->status==="fix" && $calendar["is_passed"]==false)
        <a href="javascript:void(0);" page_title="休み連絡" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/rest?student_id={{$item->id}}" role="button" class="btn btn-danger btn-sm float-left mt-1 mr-1 w-100" @if($calendar["status"]!=="fix") disabled @endif>
          <i class="fa fa-minus-circle mr-1"></i>休み連絡する
          @if($user->role==="manager" || $user->role==="teacher")
          (代理連絡）
          @endif
        </a>
        @elseif($calendar->get_member($item->user_id)->status==="confirm")
        <a href="javascript:void(0);" page_title="予定確認" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/fix?student_id={{$item->id}}" role="button" class="btn btn-primary btn-sm float-left mt-1 mr-1 w-100" @if($calendar["status"]!=="rest") disabled @endif>
          <i class="fa fa-check mr-1"></i>予定確認
          @if($user->role==="manager" || $user->role==="teacher")
          (代理連絡）
          @endif
        </a>
        @elseif($calendar->get_member($item->user_id)->status==="rest" && strtotime($calendar["start_time"]) > strtotime('now'))
        <a href="javascript:void(0);" page_title="休み取り消し" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/rest_cancel?student_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mt-1 mr-1 w-100" @if($calendar["status"]!=="rest") disabled @endif>
          <i class="fa fa-minus-circle mr-1"></i>休み取り消し連絡
          @if($user->role==="manager" || $user->role==="teacher")
          (代理連絡）
          @endif
        </a>
        @endif
      </div>
  </li>
  @endforeach
</ul>
@else
<div class="alert">
  <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
</div>
@endif
