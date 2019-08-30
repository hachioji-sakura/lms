@include('emails.common')

{{__('messages.info_calendar_add', ['trial'=>''])}}
{{__('messages.info_login_confirm')}}
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
