@include('emails.common')
{{$user_name}} 様

お申込みありがとうございます。
授業の予定を改めて、ご連絡いたします。

お申込み内容の変更・ご確認については、
下記のＵＲＬをご利用ください。

https://{{config('app.url')}}/{{$domain}}/{{$item->id}}/season_lesson?event_user_id={{$event_user_id}}&access_key={{$access_key}}

…………………………………………………………………………………………
お申込み内容
@component('emails.forms.lesson_request', ['item' => $item,  'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
