@include('emails.common')

@if($send_to==='student')
{{$user_name}}様

以下の授業をキャンセルいたしました。
ご不明な点等ございましたら、下記までお問い合わせください。　

@elseif($send_to==='teacher')
以下の授業は、生徒様都合によりキャンセルとなりました。

@endif
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………


@yield('signature')
