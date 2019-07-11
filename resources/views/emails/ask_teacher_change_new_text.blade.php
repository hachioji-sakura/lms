@include('emails.common')
{{__('messages.mail_dear_teacher', ['user_name' => $user_name])}}

{{__('messages.info_teacher_change1')}}
{{__('messages.info_teacher_change2')}}

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
