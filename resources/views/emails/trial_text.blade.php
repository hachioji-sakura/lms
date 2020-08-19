@include('emails.common')
{{$user_name}} 様

{{__('messages.trial_entry1')}}

{{__('messages.trial_entry2')}}

…………………………………………………………………………………………
体験授業お申込み内容
@component('emails.forms.trial', ['item' => $item,  'login_user' => $login_user]) @endcomponent
…………………………………………………………………………………………

@yield('signature')
