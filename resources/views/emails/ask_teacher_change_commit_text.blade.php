@include('emails.common')

@if($is_send_to_target_user)
{{__('messages.info_teacher_change_commit')}}
@else
{{__('messages.info_teacher_change_thanks')}}
@endif

…………………………………………………………………………………………
@component('emails.forms.calendar', ['item' => $item, 'send_to' => $send_to, 'login_user' => $login_user, 'notice' => '']) @endcomponent
…………………………………………………………………………………………

@yield('signature')
