@include('emails.common')
{{$user_name}} 様
@component('asks.forms.agreement_policy', []) @endcomponent
1, 2, 3, 4をご了承していただけますならば、
以下のURLからご入会登録をお願いいたします。

…………………………………………………………………………………………
入会登録画面
{{config('app.url')}}/asks/{{$ask->id}}/agreement?key={{$target_model->parent->user->access_key}}

【重要】欠席連絡について

担当講師 + 八王子さくらサポートセンターの二つのアドレスを
宛先へ加えて下さい。両方のアドレスの片方が欠けてしまうと
お休み手続きが完了しませんのでご注意ください。
…………………………………………………………………………………………

どうぞよろしくお願い申し上げます。

SaKuRa One代表
弓削 主哉

@yield('signature')
