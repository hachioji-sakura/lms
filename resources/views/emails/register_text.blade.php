@include('emails.common')
@yield('mail_title')
@if($send_to=="teacher")
{{$name_last}} {{$name_first}}先生

講師登録が完了いたしました。
引き続き、下記ログイン画面よりご利用ください。
@else
{{$parent_name_last}} {{$parent_name_first}}様

{{$name_last}} {{$name_first}}様の生徒登録が完了いたしました。
下記のログイン画面よりご利用ください。
@endif
…………………………………………………………………………………………
ログイン画面
{{config('app.url')}}/login

ご利用方法について
{{config('app.url')}}/manual

その他ご質問等（FAQ）
{{config('app.url')}}/faq

…………………………………………………………………………………………

@yield('signature')
