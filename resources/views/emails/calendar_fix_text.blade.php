@include('emails.common')

@if($send_to==='student')
{{$user->name()}}様
以下の授業予定を確定いたしました。
@elseif($send_to==='teacher')
{{$user->name()}}先生
生徒様よりご連絡があり、
以下の 授業予定が確定となりました。

@endif

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@if($send_to==='student')
…………………………………………………………………………………………
授業をお休みする場合は、以下の画面よりご連絡ください。
{{config('app.url')}}/calendars/{{$item['id']}}/status_update/rest?key={{$token}}&user={{$user->user_id}}

詳細のご確認については、以下の画面をご利用ください
{{config('app.url')}}/calendars/{{$item['id']}}?user={{$user->user_id}}

予定変更につきまして
{{config('app.url')}}/faq2
…………………………………………………………………………………………

ご不明な点等ございましたら、下記までお問い合わせください。　
@elseif($send_to==='teacher')
ご確認いただきますよう、宜しくお願い致します。
@endif

@yield('signature')
