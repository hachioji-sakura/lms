@include('emails.common')

@if($send_to=='student')
以下の授業の休み取り消し依頼を送信しました。
@else
{{__('messages.info_rest_cancel')}}
@endif
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
