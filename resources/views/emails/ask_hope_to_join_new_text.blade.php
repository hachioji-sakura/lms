@include('emails.common')
{{$user_name}} 様
体験授業をお受けいただきありがとうございます。

下記URLの画面より、入会希望・授業開始希望日のご連絡をいただきますよう、
よろしくお願いいたします。

…………………………………………………………………………………………
{{config('app.url')}}/asks/{{$ask->id}}/hope_to_join?key={{$ask->access_key}}
…………………………………………………………………………………………

…………………………………………………………………………………………
体験授業お申込み内容
@component('emails.forms.trial', ['item' => $target_model,  'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………


@yield('signature')
