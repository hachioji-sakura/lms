@include('emails.common')

{{__('messages.info_calendar_add', ['trial'=>'通常'])}}
{{__('messages.info_login_confirm')}}
…………………………………………………………………………………………
@component('emails.forms.calendar_setting', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
