■{{$item->teaching_name()}}
{{__('labels.datetime')}}：{{$item['datetime']}}
{{__('labels.place')}}：{{$item['place_floor_name']}}
--------------------------------------------
（{{__('labels.details')}}）
{{__('labels.teachers')}}：{{$item['teacher_name']}}
{{__('labels.lesson')}}：{{$item['lesson']}}
{{__('labels.lesson_type')}}：{{$item['course']}}
{{__('labels.subject')}}：{{implode(',', $item['subject'])}}
@if($send_to!=='student')
{{__('labels.students')}}：
@foreach($item->members as $member)
@if($member->user->details('students')->role=="student")
{{$member->user->details('students')["name"]}}:  {{$member->status_name()}}
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
@if($send_to!=='student')
({{__('labels.control')}}：{{$login_user["name"]}})
@endif
