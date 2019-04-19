@include('emails.common')

以下の体験授業の予定をキャンセルいたしました。
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
