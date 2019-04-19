@include('emails.common')

@if($send_to==='student')
{{$user->name()}}様

以下の授業をキャンセルいたしました。
ご不明な点等ございましたら、下記までお問い合わせください。　

@elseif($send_to==='teacher')
以下の授業は、生徒様都合によりキャンセルいたしました。

@endif
…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item]) @endcomponent
…………………………………………………………………………………………


@yield('signature')
