@include('emails.common')

@if($item->work==9)
繰り返しスケジュールを追加しました。
@else
{{__('messages.info_calendar_add', ['trial'=>'通常'])}}
@endif
{{__('messages.info_login_confirm')}}
【要対応：本日まで】(Response required: Until today)

繰り返し通常授業予定の承認をお願いします。
(Please repeatedly approve the regular lesson schedule.)

以下方法で、必ず、繰り返し授業予定をSaKuRa One Netでご承認ください。
講師にSaKuRa One Netで承認していただいて、初めて、生徒様にご連絡がいきます。
(Please be sure to approve your recurring class with SaKuRa One Net as follows.
We will contact the students for the first time after the instructor has approved it on SaKuRa One Net.)

【方法】
1. SaKuRa One Netにログイン
(Login to SaKuRa One Net)
sakuraone.jp/login

2. 通常授業設定（確認）をクリック
＊黄色の数値が、調整中の通常授業設定の数を示しています。
(Click Regular Lesson Settings (Confirm Button))

3. 予定を確定するボタンを押す
(Press the button to confirm the schedule)

…………………………………………………………………………………………
@component('emails.forms.calendar_setting', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user, 'notice' => '']) @endcomponent
…………………………………………………………………………………………

@yield('signature')
