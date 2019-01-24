@include('emails.common')
@yield('mail_title')

{{$parent_name_last}} {{$parent_name_first}}様
{{$name_last}} {{$name_first}}様の生徒登録が完了いたしました。
下記のログイン画面よりご利用ください。

…………………………………………………………………………………………
ログイン画面
{{config('app.url')}}/login

ご利用方法について
{{config('app.url')}}/manual

その他ご質問等（FAQ）
{{config('app.url')}}/faq

…………………………………………………………………………………………

@yield('signature')
