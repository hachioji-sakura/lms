@include('emails.common')

@if($send_to==='student')
{{$user_name}} 様

@if($item->trial_id > 0 && $item->is_teaching()==true)
この度は、体験授業のお申込み、誠にありがとうございます。
@endif

@if($item['exchanged_calendar_id'] > 0)振替@endif予定を登録いたしました。
現時点では、まだ、予定は確定しておりません。

以下のURLより承認をしていただけますと、この予定は確定します。

{{config('app.url')}}/calendars/{{$item['id']}}/status_update/fix?key={{$token}}&user={{$user->user_id}}

ご不明な点等ございましたら、下記までお問い合わせください。　

@elseif($send_to==='teacher' || $send_to==='manager')
{{__('messages.info_calendar_remind1')}}

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user, 'notice' => '']) @endcomponent
…………………………………………………………………………………………

{{__('messages.info_calendar_remind2')}}
@endif

@yield('signature')
