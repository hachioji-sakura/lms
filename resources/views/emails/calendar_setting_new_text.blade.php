@include('emails.common')

@if($item->work==9)
繰り返しスケジュールを追加しました。
@else
{{__('messages.info_calendar_add', ['trial'=> __('labels.regular')])}}
@endif
{{__('messages.info_login_confirm')}}
…………………………………………………………………………………………
@component('emails.forms.calendar_setting', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
