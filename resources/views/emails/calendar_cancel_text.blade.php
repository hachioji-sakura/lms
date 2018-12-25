@include('emails.common')
@yield('mail_title')

@if($send_type==='teacher')
{{$user_name}}先生
生徒様よりご連絡があり、
以下の 予定はキャンセルとなりました。
@else
{{$user_name}}様

いつも授業をお受け頂き、誠にありがとうございます。
以下の授業ですが、講師の都合により休講とさせてください。
@endif

…………………………………………………………………………………………
開始日時：{{$item['start_time']}}
終了日時：{{$item['end_time']}}
@if($send_type==='teacher')
生徒：{{$item['student_name']}}
@else
講師：{{$item['teacher_name']}}
@endif
レッスン：{{$item['lesson']}}
コース：{{$item['course']}}
科目：{{$item['subject']}}
…………………………………………………………………………………………

@if($send_type==='teacher')
ご確認いただきますよう、
宜しくお願い致します。
@else
ご迷惑をおかけ致しまして誠に申し訳ございません。
どうぞ宜しくお願い申し上げます。
@endif

@yield('signature')
