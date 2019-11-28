@include('emails.common')

@if($send_to==='student')
{{$user_name}} 様
@endif
以下の授業は欠席となりました。

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@if($send_to==='student')
ご不明な点等ございましたら、下記までお問い合わせください。　
@endif

@yield('signature')
