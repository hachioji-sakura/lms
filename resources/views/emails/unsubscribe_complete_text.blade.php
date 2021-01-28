@include('emails.common')

{{$student_name}} 様は、下記の日付にて退会予定となります。

{{__('labels.unsubscribe')}}{{__('labels.day')}}: {{$unsubscribe_date}}

振替授業については、退会予定日までに行うように、
授業の登録をお願いいたします。

…………………………………………………………………………………………
@yield('signature')
