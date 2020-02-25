@include('emails.common')
@if($send_to==='student')
{{$user_name}} 様
以下の授業予定を確定いたしました。
@elseif($send_to==='teacher' || $send_to==='manager')
{{__('messages.mail_dear_teacher', ['user_name' => $user->name()])}}
{{__('messages.info_calendar_fix')}}
@endif
…………………………………………………………………………………………
@component('emails.forms.calendar_setting', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………
@if($send_to==='student')
…………………………………………………………………………………………
詳細のご確認については、以下の画面をご利用ください
{{config('app.url')}}/calendars/{{$item['id']}}?user={{$user->user_id}}
…………………………………………………………………………………………

ご不明な点等ございましたら、下記までお問い合わせください。　
@elseif($send_to==='teacher')
{{__('messages.message_please_calendar_confirm')}}
@endif

@yield('signature')
