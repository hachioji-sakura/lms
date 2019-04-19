@include('emails.common')

@if($send_to==='student')
{{$user->name()}}様

以下の授業のお休み連絡を承りました。

@elseif($send_to==='teacher')
{{$user->name()}}先生

{{$item['target_student']->name()}}様より、
以下の 授業予定のお休み連絡をいただきました。

@if($item['status'] =='rest')
また、この授業予定はお休みとなりますので、
何卒、よろしくお願いいたします。
@else
引き続き、出席予定の生徒様への授業をよろしくお願いいたします。
@endif

@endif
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item]) @endcomponent
…………………………………………………………………………………………

@if($send_to==='student')
ご不明な点等ございましたら、下記までお問い合わせください。　
@endif

@yield('signature')
