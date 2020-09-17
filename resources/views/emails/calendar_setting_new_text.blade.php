@include('emails.common')

@if($item->work==9)
繰り返しスケジュールを追加しました。
@else
【要対応：本日まで】
繰り返し通常授業予定の承認をお願いします。

以下方法で、必ず、通常授業予定をSaKuRa One Netでご承認ください。
講師にSaKuRa One Netで承認していただいて、初めて、生徒様にご連絡がいきます。

【方法】
1. SaKuRa One Netにログイン
sakuraone.jp/login

2. 通常授業設定（確認）をクリック
＊黄色の数値が、調整中の通常授業設定の数を示しています。

3. 予定を確定するボタンを押す
@endif

…………………………………………………………………………………………
@component('emails.forms.calendar_setting', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user, 'notice' => '']) @endcomponent
…………………………………………………………………………………………

@yield('signature')
