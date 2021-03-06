@include('emails.common')

@if($send_to==='student')
{{$user_name}} 様

以下の授業をキャンセルいたしました。
ご不明な点等ございましたら、下記までお問い合わせください。　

@elseif($send_to==='teacher' || $send_to==='manager')
{{__('messages.info_calendar_cancel')}}
@endif

…………………………………………………………………………………………
@component('emails.forms.calendar_setting', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user, 'notice' => $notice]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
