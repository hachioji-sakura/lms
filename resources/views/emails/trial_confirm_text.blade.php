@include('emails.common')

体験授業の予定を追加いたしました。
マイページにログインし、ご確認ください。
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
