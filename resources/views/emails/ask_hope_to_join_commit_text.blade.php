@include('emails.common')
{{$user_name}} 様

SaKuRa One代表の弓削主哉（ゆげかずや）です。
ご入会希望のご連絡を頂き、大変感謝致します。

改めて、通塾スケジュールについて、
ご連絡をいたしますので、お待ちください。

どうぞよろしくお願い申し上げます。
…………………………………………………………………………………………
ご入会お申込み内容
@component('emails.forms.trial', ['item' => $target_model,  'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………


SaKuRa One代表
弓削 主哉

@yield('signature')
