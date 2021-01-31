@include('emails.common')

{{__('messages.info_unsubscribe_for_teacher1', ['student_name'=>$student->name()])}}

{{__('labels.unsubscribe')}}{{__('labels.day')}}: {{$unsubscribe_date}}

{{__('messages.info_unsubscribe_for_teacher2')}}

…………………………………………………………………………………………
@yield('signature')
