@include('emails.common')
{{$user_name}} 様
季節講習の勤務可能日時の登録が完了しました。
修正する場合は、以下のＵＲＬをご利用ください。

https://{{config('app.url')}}/{{$domain}}/{{$item->id}}/season_lesson?event_user_id={{$event_user_id}}&access_key={{$access_key}}

…………………………………………………………………………………………
お申込み内容
@component('emails.forms.lesson_request', ['item' => $item,  'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
