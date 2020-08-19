@include('emails.common')

{{__('labels.system_name')}}　アカウント登録について
本登録用のURLをご案内いたします。

下記のURLより、アカウント登録をお願いいたします。

…………………………………………………………………………………………
本登録画面
{{config('app.url')}}/register?key={{$access_key}}

※URLの有効期限はこのメールの受信から20日以内となっています。
…………………………………………………………………………………………


@yield('signature')
