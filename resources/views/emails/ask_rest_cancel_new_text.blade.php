@include('emails.common')

以下の 授業予定の休み取り消し依頼が登録されました。
依頼画面にて、承認もしくは、差し戻しを操作してください

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
