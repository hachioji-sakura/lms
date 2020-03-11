@include('emails.common')
{{$user_name}} 様
体験授業をお受けいただきありがとうございます。

下記URLの画面より、入会希望・授業開始希望日のご連絡をいただきますよう、
よろしくお願いいたします。

…………………………………………………………………………………………
{{config('app.url')}}/asks/{{$ask->id}}/hope_to_join?key={{$target_model->parent->user->access_key}}
…………………………………………………………………………………………

@yield('signature')
