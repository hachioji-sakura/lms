<?php
if(empty($item["lesson"]) || empty($item["work_name"])){
  $item = $item->details(1);
}
 ?>
 @if(isset($notice) && !empty($notice))
 ■{{__('labels.notice')}}:
 {{$notice}}
 @endif

@if($item->is_teaching()==true)
■{{__('labels.regular_schedule_setting')}}
@else
■{{$item['work_name']}}
@endif
@if($item->is_teaching()==true && !empty($item['schedule_start_date']))
{{__('labels.schedule_start_date')}}：{{$item['schedule_start_date']}}
@endif
{{__('labels.repeat')}}：{{$item['repeat_setting_name']}}
{{__('labels.place')}}：{{$item['place_floor_name']}}@if($item->is_online()==true)/{{__('labels.online')}}@endif

@if($send_to!=='student')
({{__('labels.status')}}：{{$item->status_name()}})
@endif
--------------------------------------------
（{{__('labels.details')}}）
@if($item->work==9)
{{__('labels.charge_user')}}：{{$item->user->details()->name()}}
@else
{{__('labels.teachers')}}：{{$item['teacher_name']}}
@if($item->is_teaching()==true)
{{__('labels.lesson')}}：{{$item['lesson']}}
{{__('labels.lesson_type')}}：{{$item['course']}}
{{__('labels.subject')}}：{{implode(',', $item['subject'])}}
@endif
@if($send_to!=='student')
{{__('labels.students')}}：
@foreach($item->members as $member)
@if($member->user->details('students')->role=="student")
{{$member->user->details('students')["name"]}}
@endif
@endforeach
@endif
@endif
--------------------------------------------
@if(!empty($item->remark))
{{__('labels.remark')}}:
{{$item->remark}}
@endif
@isset($item['comment'])
{{__('labels.notice')}}{{$item['comment']}}
@endisset
@if($send_to!=='student' && (!isset($is_control) || $is_control==false))
({{__('labels.control')}}：{{$login_user["name"]}})
@endif
