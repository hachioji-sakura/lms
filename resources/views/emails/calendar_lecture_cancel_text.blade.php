@include('emails.common')

{{$user_name}} 様
いつも授業をお受け頂き、誠にありがとうございます。

以下の授業ですが、講師の都合により休講とさせてください。
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user, 'notice' => '']) @endcomponent
…………………………………………………………………………………………

ご迷惑をおかけ致しまして誠に申し訳ございません。
どうぞ宜しくお願い申し上げます。

@yield('signature')
