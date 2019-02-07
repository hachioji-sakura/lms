@include('emails.common')
@yield('mail_title')

@if($send_to=="teacher")
{{$user_name}}先生
本メールにより、講師仮登録完了しました。

引き続き、以下の URL より本登録を行ってください。
※URLの有効期限はお申込みから24時間以内となっています。
…………………………………………………………………………………………
本登録
{{config('app.url')}}/teachers/register?key={{$access_key}}
…………………………………………………………………………………………
@else
{{$user_name}}様

ご入会お申込みいただき、誠にありがとうございます。

@isset($remind)

仮登録の状態が残っておりますので、お知らせいたします。

お手数をおかけしますが、
@else

本メールにより、お申込みを仮受付しております。
@endisset
以下の URL より生徒情報の登録を行ってください。
※URLの有効期限はお申込みから24時間以内となっています。
…………………………………………………………………………………………
ユーザー登録
{{config('app.url')}}/register?key={{$access_key}}
…………………………………………………………………………………………
@endif

@yield('signature')
