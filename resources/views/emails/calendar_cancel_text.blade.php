@include('emails.common')

@if($send_to==='student')
{{$user_name}}様

以下の授業をキャンセルいたしました。
ご不明な点等ございましたら、下記までお問い合わせください。　

@elseif($send_to==='teacher')
以下の授業は、生徒様都合によりキャンセルいたしました。

@endif
…………………………………………………………………………………………
開始日時：{{$item['start_time']}}
終了日時：{{$item['end_time']}}
生徒：{{$item['student_name']}}
講師：{{$item['teacher_name']}}
@if($item['trial_id'] > 0)
レッスン：体験授業
@else
レッスン：{{$item['lesson']}}
コース：{{$item['course']}}
科目：{{$item['subject']}}
@endif
キャンセル理由:{{$item['cancel_reason']}}
…………………………………………………………………………………………


@yield('signature')
