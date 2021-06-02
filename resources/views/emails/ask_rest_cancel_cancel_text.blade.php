@include('emails.common')

以下の予定の休み取り消し依頼について
差戻しました。

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user, 'notice' => '']) @endcomponent
…………………………………………………………………………………………

@yield('signature')
