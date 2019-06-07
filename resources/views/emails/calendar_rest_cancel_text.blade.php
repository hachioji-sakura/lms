@include('emails.common')

@if($send_to==='student')
{{$user["name"]}}様
以下の授業の休み取り消し連絡を承りました。
{{-- 現状は生徒あてに連絡はしない --}}
@elseif($send_to==='teacher')
{{$user["name"]}}先生
@if($is_proxy===true)
以下の 授業予定の休み取り消し連絡をしました。
@else
{{$login_user["name"]}}様より、以下の 授業予定の休み取り消し連絡をいただきました。
@endif

@endif
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@if($send_to==='student')
ご不明な点等ございましたら、下記までお問い合わせください。　
@endif

@yield('signature')
