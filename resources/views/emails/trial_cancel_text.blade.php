@include('emails.common')

以下の体験授業の予定をキャンセルいたしました。
…………………………………………………………………………………………
●体験授業
開始日時：{{$item['start_time']}}
終了日時：{{$item['end_time']}}
生徒：{{$item['student_name']}}
講師：{{$item['teacher_name']}}
…………………………………………………………………………………………

@yield('signature')
