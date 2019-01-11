@include('emails.common')
@yield('mail_title')

{{$user_name}}様

以下の URL よりパスワードの再発行を行って下さい。
※URLの有効期限はお申込みから24時間以内となっています。
…………………………………………………………………………………………
パスワードの再発行
{{config('app.url')}}/password/setting?key={{$access_key}}
…………………………………………………………………………………………

@yield('signature')
