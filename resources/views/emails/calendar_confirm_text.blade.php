@include('emails.common')
@yield('mail_title')

@if($send_to==='student' && ($is_child===true || $is_own===true))
{{$user_name}}様
{{$user_name}} 保護者様

以下の授業予定を追加いたしました。

学習管理システムにて、
授業予定のご確認をお願いいたします。
@elseif($send_to==='teacher')
以下の授業予定を追加いたしました。

生徒様から授業予定の確定操作後に、
予定は確定となります。
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
