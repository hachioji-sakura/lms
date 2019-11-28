@include('emails.common')

@if($send_to==='student')
{{$user["name"]}} 様
以下の授業のお休み連絡を承りました。

@elseif($send_to==='teacher' || $send_to==='manager')
{{__('messages.mail_dear_teacher', ['user_name' => $user["name"]])}}

@if($is_proxy===true)
{{__('messages.info_calendar_rest_proxy')}}
@else
{{__('messages.info_calendar_rest', ['user_name' => $login_user["name"]])}}
@endif

@if($item->is_group()==true)
@if($item['status'] =='rest')
{{__('messages.info_calendar_rest_to_cancel')}}
@else
{{__('messages.info_calendar_rest_to_fix')}}
@endif
@endif

@endif
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@if($send_to==='student')
ご不明な点等ございましたら、下記までお問い合わせください。　
@endif

@yield('signature')
