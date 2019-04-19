@include('emails.common')

体験授業の予定を追加いたしました。
マイページにログインし、ご確認ください。
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
