@include('emails.common')
{{$user_name}} 様

SaKuRa One代表の弓削主哉（ゆげかずや）です。
ご入会のご連絡を頂き、大変感謝致します。

大変お手数ですが、以下のフォームにご記入いただき、
システムへのユーザー登録をしていただけますと幸いです。
…………………………………………………………………………………………
入会登録画面
{{config('app.url')}}/asks/{{$ask->id}}/agreement?key={{$access_key}}
…………………………………………………………………………………………

どうぞよろしくお願い申し上げます。

SaKuRa One代表
弓削 主哉

@yield('signature')
