@include('emails.common')

@if($send_to==='student')
{{$user_name}} 様

現時点では、まだ、通常授業の予定は確定しておりません。
以下のURLより、通常授業予定の承認をしていただけますと、この予定は確定します。

{{config('app.url')}}/calendar_settings/{{$item['id']}}/status_update/fix?key={{$token}}&user={{$user->user_id}}

ご不明な点等ございましたら、下記までお問い合わせください。　

@elseif($send_to==='teacher' || $send_to==='manager')
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

{{__('messages.info_calendar_remind2')}}
@endif

@yield('signature')
