@include('emails.common')

{!!nl2br(__('messages.info_teacher_change_thanks'))!!}

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
