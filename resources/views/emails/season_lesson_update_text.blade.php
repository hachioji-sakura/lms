@include('emails.common')

{{$user_name}} 様

お申込み内容を変更いたしました。

お申込み内容の変更・ご確認については、
下記のＵＲＬをご利用ください。

{{config('app.url')}}/students/{{$domain_item_id}}/season_lesson?event_user_id={{$event_user_id}}&access_key={{$access_key}}

…………………………………………………………………………………………
    お申込み内容({{__('labels.after_change')}})
…………………………………………………………………………………………
@component('emails.forms.'.$item->type, ['item' => $item]) @endcomponent
…………………………………………………………………………………………


@yield('signature')
