@include('emails.common')

{{$user_name}} 様
いつも授業をお受け頂き、誠にありがとうございます。

以下の授業予定について、
担当予定の{{$prev_teacher_name}}先生ですが、
都合により、{{$next_teacher_name}}先生が行います。
何卒、ご了承ください。

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

ご迷惑をおかけ致しまして誠に申し訳ございません。
どうぞ宜しくお願い申し上げます。

@yield('signature')
