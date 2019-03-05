@include('emails.common')

@if($send_to==='student')
{{$user_name}}様

以下の授業予定を確定いたしました。
授業当時、忘れずにお越しください。

…………………………………………………………………………………………
授業をお休みする場合は、以下の画面よりご連絡ください。
{{config('app.url')}}/calendars/{{$item['id']}}/rest?user={{$user_id}}

予定変更について
{{config('app.url')}}/faq2
…………………………………………………………………………………………

@elseif($send_to==='teacher')
{{$user_name}}先生
生徒様よりご連絡があり、
以下の 授業予定が確定となりました。

@endif

…………………………………………………………………………………………
開始日時：{{$item['start_time']}}
終了日時：{{$item['end_time']}}
生徒：{{$item['student_name']}}
講師：{{$item['teacher_name']}}
レッスン：{{$item['lesson']}}
コース：{{$item['course']}}
科目：{{$item['subject']}}
…………………………………………………………………………………………

@if($send_to==='student')
ご不明な点等ございましたら、下記までお問い合わせください。　
@elseif($send_to==='teacher')
ご確認いただきますよう、宜しくお願い致します。
@endif

@yield('signature')
