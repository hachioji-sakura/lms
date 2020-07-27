<?php
if(empty($item["lesson"]) || empty($item["work_name"])){
  $item = $item->details(1);
}
 ?>
@if($item->is_teaching()==true)
■{{$item->teaching_type_name()}}
@else
■{{$item['work_name']}}
@endif
{{__('labels.datetime')}}：{{$item['datetime']}}
{{__('labels.place')}}：{{$item['place_floor_name']}}@if($item->is_online()==true)/{{__('labels.online')}}@endif

@if($send_to!=='student')
({{__('labels.status')}}：{{$item->status_name()}})
@endif
--------------------------------------------
（{{__('labels.details')}}）
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
{{$member->user->details('students')["name"]}}({{$member->status_name()}})
@endif
@endforeach
@endif

--------------------------------------------
@isset($item['comment'])
{{__('labels.notice')}}{{$item['comment']}}
@endisset
@if(isset($item['cancel_reason']) && !empty($item['cancel_reason']))
{{__('labels.cencel_reason')}}:{{$item['cancel_reason']}}
@endif
@if($send_to!=='student' && (!isset($is_control) || $is_control==false))
({{__('labels.control')}}：{{$login_user["name"]}})
@endif
