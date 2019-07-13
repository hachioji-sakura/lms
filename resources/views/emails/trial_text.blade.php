@include('emails.common')
{{$user_name}}様

{!!nl2br(__('messages.trial_entry1'))!!}


{!!nl2br(__('messages.trial_entry2'))!!}

@yield('signature')
