@include('emails.common')
以下、体験授業予定を確認してください。

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $calendar, 'send_to' => 'teacher', 'login_user' => $login_user, 'is_control' => false]) @endcomponent
…………………………………………………………………………………………
@yield('signature')
