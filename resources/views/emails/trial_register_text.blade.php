@include('emails.common')
{{$user_name}}様
この度は入会を希望して頂き、誠に感謝しております。

お手数をおかけしますが、
以下のURLからご入会登録をお願いいたします。

…………………………………………………………………………………………
入会登録画面
{{config('app.url')}}/trials/{{$item['id']}}/admission?key={{$access_key}}

当塾のシステムにつきまして
{{config('app.url')}}/faqs
…………………………………………………………………………………………
@if(!empty($comment))
{{$comment}}
@endif
@yield('signature')
