@include('emails.common')
{{$user_name}} 様

体験授業希望日時変更いたしました。

…………………………………………………………………………………………
変更後
@component('emails.forms.trial', ['item' => $item,  'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
