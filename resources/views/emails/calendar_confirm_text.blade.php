@include('emails.common')

@if($send_to==='student')
{{$user->name()}}様

@if($item['trial_id'] > 0)
この度は、体験授業のお申込み、誠にありがとうございます。
@endif
以下のURLより、授業予定のご確認をお願いいたします。
{{config('app.url')}}/calendars/{{$item['id']}}/fix?key={{$token}}&user={{$user->user_id}}

ご不明な点等ございましたら、下記までお問い合わせください。　

@elseif($send_to==='teacher')
以下の授業予定を生徒様にご連絡いたしました。

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item]) @endcomponent
…………………………………………………………………………………………

生徒様から授業予定の確定操作後に、
予定は確定となります。
@endif


@yield('signature')
