@include('emails.common')

体験授業の予定を追加いたしました。
マイページからご確認ください。
…………………………………………………………………………………………
●体験授業
開始日時：{{$item['start_time']}}
終了日時：{{$item['end_time']}}
生徒：{{$item['student_name']}}
講師：{{$item['teacher_name']}}
…………………………………………………………………………………………

@yield('signature')
