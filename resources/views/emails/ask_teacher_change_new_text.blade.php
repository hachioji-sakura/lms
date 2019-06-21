@include('emails.common')
{{$user_name}}先生

以下の授業予定の代講依頼をご連絡いたしました。
代講可能な場合、代講依頼を承認してください。

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
