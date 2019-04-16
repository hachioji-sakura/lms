@include('emails.common')

@if($send_to==='student')
{{$user_name}}様

以下の授業のお休み連絡を承りました。

@elseif($send_to==='teacher')
{{$user_name}}先生
生徒様よりご連絡があり、
以下の 授業予定はお休みとなりました。

@endif

…………………………………………………………………………………………
開始日時：{{$item['start_time']}}
終了日時：{{$item['end_time']}}
生徒：{{$item['student_name']}}
講師：{{$item['teacher_name']}}
レッスン：{{$item['lesson']}}
コース：{{$item['course']}}
科目：{{implode(',', $item['subject'])}}
休み理由:{{$item['rest_reason']}}
…………………………………………………………………………………………

@if($send_to==='student')
ご不明な点等ございましたら、下記までお問い合わせください。　
@elseif($send_to==='teacher')
ご確認いただきますよう、宜しくお願い致します。
@endif

@yield('signature')
