@include('emails.common')

授業予定を追加いたしました。
マイページにログインし、ご確認ください。
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
