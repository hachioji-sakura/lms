@if(isset($notice) && !empty($notice))
■{{__('labels.notice')}}:
{{$notice}}
@endif

@if($item->is_teaching()==true)
■{{$item->teaching_type_name()}}
@else
■{{$item->work()}}
@endif
{{__('labels.datetime')}}：{{$item->datetime()}}
{{__('labels.place')}}：{{$item->place_floor_name()}}@if($item->is_online()==true)/{{__('labels.online')}}@endif

@if($send_to!=='student')
({{__('labels.status')}}：{{$item->status_name()}})
@endif
--------------------------------------------
（{{__('labels.details')}}）
@if($item->is_teaching()==true)
{{__('labels.teachers')}}：{{$item->user->details('teachers')->name()}}
{{__('labels.lesson')}}：{{$item->lesson()}}
{{__('labels.lesson_type')}}：{{$item->course()}}
{{__('labels.subject')}}：{{implode(',', $item->subject())}}
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
@if($send_to!=='student' && (!isset($is_control) || $is_control==false))
({{__('labels.control')}}：{{$login_user["name"]}})
@endif
