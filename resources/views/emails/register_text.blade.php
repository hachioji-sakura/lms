@include('emails.common')
@yield('mail_title')
@if($send_to=="teacher")
{{$name_last}} {{$name_first}}先生

講師登録が完了いたしました。
引き続き、下記ログイン画面よりご利用ください。
@elseif($send_to=="manager")
{{$name_last}} {{$name_first}}さん

事務登録が完了いたしました。
引き続き、下記ログイン画面よりご利用ください。
@elseif($send_to=="parent")
{{$parent_name_last}} {{$parent_name_first}}様
この度、入会お申込み誠にありがとうございます。

下記のログイン画面より、学習管理システムをご利用ください。
@endif
…………………………………………………………………………………………
ログイン画面
@if($send_to=="manager")
{{config('app.url')}}/managers/login
@else
{{config('app.url')}}/login
@endif

ご利用方法につきまして
{{config('app.url')}}/manual

その他ご質問等（FAQ）
{{config('app.url')}}/faq

…………………………………………………………………………………………

@yield('signature')
