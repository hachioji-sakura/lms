@include('emails.common')
{{$user_name}} 様
@component('asks.forms.agreement_policy', []) @endcomponent

1, 2, 3, 4, 5をご了承していただけますならば、
以下のURLからご入会登録をお願いいたします。

…………………………………………………………………………………………
入会登録画面
{{config('app.url')}}/asks/{{$ask->id}}/agreement?key={{$access_key}}

…………………………………………………………………………………………

どうぞよろしくお願い申し上げます。

SaKuRa One代表
弓削 主哉

@yield('signature')
{{--
TODO このメールはつかっていない？・その場合、削除
--}}
