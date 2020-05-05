@include('emails.common')

@if($item->target_user->role == "parent")
{{__('messages.mail_dear',['user_name' => $item->target_user->details()->name])}}
@elseif($item->target_user->role == "teacher")
{{__('messages.mail_dear_teacher',['user_name' => $item->target_user->details()->name])}}
@elseif($item->target_user->role == "manager")
{{__('messages.mail_dear_manager',['user_name' => $item->target_user->details()->name])}}
@endif

@if($item->create_user->role == "parent")
{{__('messages.mail_dear',['user_name' => $item->create_user->details()->name])}}
@elseif($item->create_user->role == "teacher")
{{__('messages.mail_dear_teacher',['user_name' => $item->create_user->details()->name])}}
@elseif($item->create_user->role == "manager")
{{__('messages.mail_dear_manager',['user_name' => $item->create_user->details()->name])}}
@endif

--------------------------------------
{{__('labels.title')}}:{{$item->title}}

{{__('labels.body')}}
{{$item->body}}


@yield('signature')
