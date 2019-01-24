@include('emails.common')
@yield('mail_title')

{{$user_name}}様

ご入会お申込みいただき、誠にありがとうございます。
本メールにより、お申込みを仮受付しております。

以下の URL より生徒情報の登録を行ってください。
※URLの有効期限はお申込みから24時間以内となっています。
…………………………………………………………………………………………
ユーザー登録
{{config('app.url')}}/register?key={{$access_key}}
…………………………………………………………………………………………

@yield('signature')
