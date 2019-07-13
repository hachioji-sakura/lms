@include('emails.common')

{!!nl2br(__('messages.info_calendar_add', ['trial'=>'']))!!}
{!!nl2br(__('messages.info_login_confirm'))!!}
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
