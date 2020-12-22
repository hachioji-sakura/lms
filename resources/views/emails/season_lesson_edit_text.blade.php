@include('emails.common')
{{$user_name}} 様

お申込み内容を変更しました。
https://{{config('app.url')}}/{{$domain}}/{{$item->id}}/season_lesson?event_user_id={{$event_user_id}}&access_key={{$access_key}}

…………………………………………………………………………………………
お申込み内容
@component('emails.forms.lesson_request', ['item' => $item,  'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
