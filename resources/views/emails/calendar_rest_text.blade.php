@include('emails.common')
@yield('mail_title')

@if($send_type==='student')
{{$user_name}}様
{{$user_name}} 保護者様

以下の授業の欠席連絡を承りました。

@elseif($send_type==='teacher')
{{$user_name}}先生
生徒様よりご連絡があり、
以下の 授業予定は欠席となりました。

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

@if($send_type==='student')
ご不明な点等ございましたら、下記までお問い合わせください。　
@elseif($send_type==='teacher')
ご確認いただきますよう、
宜しくお願い致します。
@endif

@yield('signature')
