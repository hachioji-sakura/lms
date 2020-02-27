@include('emails.common')

@if($send_to==='student')
{{$user_name}} 様

現時点では、まだ、通常授業の予定は確定しておりません。
以下のURLより、通常授業予定の承認をしていただけますと、この予定は確定します。

{{config('app.url')}}/calendar_settings/{{$item['id']}}/status_update/fix?key={{$token}}&user={{$user->user_id}}

ご不明な点等ございましたら、下記までお問い合わせください。　

@elseif($send_to==='teacher' || $send_to==='manager')
{{__('messages.info_calendar_remind1')}}

…………………………………………………………………………………………
@component('emails.forms.calendar_setting', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

{{__('messages.info_calendar_remind2')}}
@endif

@yield('signature')
