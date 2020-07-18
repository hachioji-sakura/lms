@include('emails.common')

@if($send_to==='student')
{{$user["name"]}} 様
{{$mail_title}}
@elseif($send_to==='teacher' || $send_to==='manager')
{{__('messages.mail_dear_teacher', ['user_name' => $user["name"]])}}
{{$mail_title}}
{{__('messages.info_login_confirm')}}
@endif
…………………………………………………………………………………………
    {{__('labels.before_change')}}
…………………………………………………………………………………………
@component('emails.forms.calendar_setting', ['item' => $old_item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………
　　　　　　　↓↓↓
…………………………………………………………………………………………
    {{__('labels.after_change')}}
…………………………………………………………………………………………
@component('emails.forms.calendar_setting', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………


@yield('signature')
