@include('emails.common')

以下の 授業予定の休講依頼を連絡しました。
依頼承認後、休講となります。

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
