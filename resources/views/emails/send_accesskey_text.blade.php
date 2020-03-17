@include('emails.common')
@yield('mail_title')

{{__('messages.mail_dear', ['user_name' => $user_name])}}
{{__('messages.info_use_verification_code')}}

{{__('labels.verification_code')}}:{{$verification_code}}

@yield('signature')
