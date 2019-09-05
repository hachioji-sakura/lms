@include('emails.common')

@if($send_to==='student')
{{$user["name"]}}様
{{__('messages.info_calendar_update', ['trial'=>''])}}
@elseif($send_to==='teacher' || $send_to==='manager')
{{__('messages.mail_dear_teacher', ['user_name' => $user["name"]])}}
{{__('messages.info_calendar_update', ['trial'=>''])}}
{{__('messages.info_login_confirm')}}
@endif
…………………………………………………………………………………………
    {{__('labels.before_change')}}
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $old_item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………
　　　　　　　↓↓↓
…………………………………………………………………………………………
    {{__('labels.after_change')}}
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………


@yield('signature')
