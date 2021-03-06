@include('emails.common')


@if($item->target_user->details()->role == "parent" || $item->target_user->details()->role == "student")
{{__('messages.mail_dear',['user_name' => $item->target_user->details()->name])}} {{__('messages.message_first_sentence')}}
@elseif($item->target_user->details()->role == "teacher")
{{__('messages.mail_dear_teacher',['user_name' => $item->target_user->details()->name])}} {{__('messages.message_first_sentence')}}
@elseif($item->target_user->details()->role == "manager")
{{__('messages.mail_dear_manager',['user_name' => $item->target_user->details()->name])}} {{__('messages.message_first_sentence')}}
@endif

{{__('messages.mail_reply_recomend')}}
{{config('app.url')}}/login

--------------------------------------
{{__('labels.message_title')}}:
{{$item->title}}

{{__('labels.body')}}:
{{$item->body}}


@yield('signature')
